class Product {
  final String id;
  final String name;
  final String description;
  final String price;
  final String image;

  Product({
    required this.id,
    required this.name,
    required this.description,
    required this.price,
    required this.image,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
  return Product(
    id: json['id'],
    name: json['name'],
    price: json['price'],
    description: json['description'],
    image: "http://10.0.2.2/Website-PhanBon/" + json['image'],
  );
}
}
