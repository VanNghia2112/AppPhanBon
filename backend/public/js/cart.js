document.addEventListener('DOMContentLoaded', function () {
    const cartBody = document.getElementById('cart-body');

    if (cartBody) {
        cartBody.addEventListener('click', function (event) {
            const target = event.target;
            const productId = target.dataset.id;
            const input = cartBody.querySelector(`.quantity-input[data-id="${productId}"]`);

            if (!productId || !input) return;

            let currentQuantity = parseInt(input.value);

            // Xử lý nút giảm số lượng
            if (target.classList.contains('quantity-decrease')) {
                if (currentQuantity > 1) {
                    input.value = currentQuantity - 1;
                    updateCart(productId, input.value);
                }
            }

            // Xử lý nút tăng số lượng
            if (target.classList.contains('quantity-increase')) {
                input.value = currentQuantity + 1;
                updateCart(productId, input.value);
            }
            
            // Xử lý nút xóa
            if (target.classList.contains('remove-from-cart')) {
                if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                    removeFromCart(productId);
                }
            }
        });

        // Xử lý khi người dùng tự nhập số lượng
        cartBody.addEventListener('change', function(event) {
            const target = event.target;
            if (target.classList.contains('quantity-input')) {
                const productId = target.dataset.id;
                let quantity = parseInt(target.value);
                if (quantity < 1 || isNaN(quantity)) {
                    quantity = 1;
                    target.value = 1;
                }
                updateCart(productId, quantity);
            }
        });
    }

    // Hàm gửi yêu cầu cập nhật số lượng
    function updateCart(productId, quantity) {
        fetch('/Website-PhanBon/Product/updateCart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId, quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật Tạm tính của sản phẩm
                document.getElementById(`subtotal-${productId}`).innerHTML = data.itemSubtotal;
                
                // Cập nhật Tổng tiền của giỏ hàng
                document.getElementById('cart-subtotal').innerHTML = data.cartTotal;
                document.getElementById('cart-total').innerHTML = `<strong>${data.cartTotal}</strong>`;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Hàm gửi yêu cầu xóa sản phẩm
    function removeFromCart(productId) {
        fetch('/Website-PhanBon/Product/removeFromCart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Xóa dòng sản phẩm khỏi bảng
                document.getElementById(`product-row-${productId}`).remove();
                
                // Cập nhật lại tổng tiền
                document.getElementById('cart-subtotal').innerHTML = data.cartTotal;
                document.getElementById('cart-total').innerHTML = `<strong>${data.cartTotal}</strong>`;

                // Kiểm tra nếu giỏ hàng rỗng thì hiển thị thông báo
                if(data.cartEmpty) {
                    location.reload(); // Tải lại trang để hiển thị thông báo giỏ hàng trống
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
});