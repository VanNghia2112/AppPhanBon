class OrderDetail {
  final String name;
  final int quantity;
  final double price;
  final String image;
  
  OrderDetail({
    required this.name,
    required this.quantity,
    required this.price,
    required this.image,
  });

  factory OrderDetail.fromJson(Map<String, dynamic> json) {
    return OrderDetail(
      name: json["name"],
      quantity: int.parse(json["quantity"].toString()),
      price: double.parse(json["price"].toString()),
      image: "http://10.0.2.2/Website-PhanBon/" + json["image"],
    );
  }
}
