# Minimal fine-tuning script for folder datasets.
import argparse, os, torch
from torch import nn, optim
from torchvision import datasets, models, transforms
from torch.utils.data import DataLoader

def get_loaders(data_dir, img=224, bs=32):
    tfm_train = transforms.Compose([
        transforms.Resize((img,img)),
        transforms.RandomHorizontalFlip(),
        transforms.ColorJitter(0.2,0.2,0.2,0.1),
        transforms.ToTensor(),
        transforms.Normalize([0.485,0.456,0.406],[0.229,0.224,0.225])
    ])
    tfm_val = transforms.Compose([
        transforms.Resize((img,img)),
        transforms.ToTensor(),
        transforms.Normalize([0.485,0.456,0.406],[0.229,0.224,0.225])
    ])
    train_ds = datasets.ImageFolder(os.path.join(data_dir,"train"), tfm_train)
    val_ds   = datasets.ImageFolder(os.path.join(data_dir,"val"), tfm_val)
    return (DataLoader(train_ds, bs, shuffle=True, num_workers=2),
            DataLoader(val_ds, bs, shuffle=False, num_workers=2),
            train_ds.classes)

def main(args):
    train_loader, val_loader, classes = get_loaders(args.data, args.img, args.bs)
    device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
    model = models.resnet18(weights=models.ResNet18_Weights.DEFAULT)
    model.fc = nn.Linear(model.fc.in_features, len(classes))
    model.to(device)
    opt = optim.AdamW(model.parameters(), lr=args.lr)
    criterion = nn.CrossEntropyLoss()

    best = 0.0
    for epoch in range(args.epochs):
        model.train()
        total, correct, loss_sum = 0, 0, 0.0
        for x,y in train_loader:
            x,y = x.to(device), y.to(device)
            opt.zero_grad()
            logits = model(x)
            loss = criterion(logits,y)
            loss.backward(); opt.step()
            loss_sum += float(loss)*x.size(0)
            correct += (logits.argmax(1)==y).sum().item()
            total += x.size(0)
        train_acc = correct/total
        val_acc = eval_acc(model, val_loader, device)
        print(f"Epoch {epoch+1}: train_acc={train_acc:.3f} val_acc={val_acc:.3f}")

        if val_acc>best:
            best=val_acc
            os.makedirs('models', exist_ok=True)
            torch.jit.script(model.cpu()).save('models/model.pt')
            with open('models/labels.txt','w',encoding='utf-8') as f:
                f.write('\n'.join(classes))
            model.to(device)

def eval_acc(model, loader, device):
    model.eval()
    total, correct = 0, 0
    with torch.no_grad():
        for x,y in loader:
            x,y = x.to(device), y.to(device)
            logits = model(x)
            correct += (logits.argmax(1)==y).sum().item()
            total += x.size(0)
    return correct/total if total else 0.0

if __name__ == "__main__":
    ap = argparse.ArgumentParser()
    ap.add_argument("--data", required=True, help="path to data directory with train/ and val/")
    ap.add_argument("--epochs", type=int, default=10)
    ap.add_argument("--bs", type=int, default=32)
    ap.add_argument("--img", type=int, default=224)
    ap.add_argument("--lr", type=float, default=1e-3)
    args = ap.parse_args()
    main(args)
