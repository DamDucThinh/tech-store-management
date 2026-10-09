/*
 * Tech Store Management - JavaScript thuần, không dùng thư viện.
 *
 * 1. Mở/đóng menu trên điện thoại
 * 2. Hỏi xác nhận trước khi xóa
 * 3. Đóng thông báo
 * 4. Xem trước ảnh trước khi upload
 * 5. Form tạo đơn hàng: thêm/xóa dòng sản phẩm, tự tính thành tiền và tổng tiền
 */
(function () {
    'use strict';

    // 1. Menu trên điện thoại
    document.querySelectorAll('[data-sidebar-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.body.classList.toggle('sidebar-open');
        });
    });

    // 2. Form có data-confirm sẽ hỏi lại trước khi gửi
    document.addEventListener('submit', function (event) {
        var message = event.target.dataset.confirm;

        if (message && !window.confirm(message)) {
            event.preventDefault();
        }
    });

    // 3. Nút đóng thông báo
    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-dismiss="alert"]');

        if (button) {
            button.closest('.alert').remove();
        }
    });

    // 4. Xem trước ảnh: <input type="file" data-preview="#id-khung-xem-truoc">
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            var preview = document.querySelector(input.dataset.preview);
            var file = input.files[0];

            if (!preview || !file) {
                return;
            }

            var image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = 'Ảnh xem trước';
            preview.replaceChildren(image);
        });
    });

    // 5. Form tạo đơn hàng
    var orderLines = document.getElementById('order-lines');

    if (orderLines) {
        initOrderForm(orderLines);
    }

    function initOrderForm(body) {
        var template = document.getElementById('order-line-template');
        var totalElement = document.getElementById('order-total');
        var nextIndex = Number(body.dataset.nextIndex);
        var formatter = new Intl.NumberFormat('vi-VN');

        function money(value) {
            return formatter.format(Math.round(value)) + ' ₫';
        }

        function recalculate() {
            var rows = body.querySelectorAll('.order-line');
            var selected = [];
            var total = 0;

            rows.forEach(function (row) {
                var select = row.querySelector('.product-select');
                var option = select.selectedOptions[0];
                var price = option && option.dataset.price ? Number(option.dataset.price) : 0;
                var quantity = Math.max(0, parseInt(row.querySelector('.qty-input').value, 10) || 0);

                row.querySelector('.unit-price').textContent = price ? money(price) : '—';
                row.querySelector('.line-total').textContent = price ? money(price * quantity) : '—';
                row.querySelector('.remove-line').disabled = rows.length === 1;

                if (select.value) {
                    selected.push(select.value);
                }

                total += price * quantity;
            });

            // Không cho chọn cùng một sản phẩm ở hai dòng: khóa lựa chọn đã dùng ở dòng khác.
            body.querySelectorAll('.product-select').forEach(function (select) {
                Array.prototype.forEach.call(select.options, function (option) {
                    option.disabled = option.value !== '' && option.value !== select.value && selected.indexOf(option.value) !== -1;
                });
            });

            totalElement.textContent = money(total);
        }

        document.getElementById('add-line').addEventListener('click', function () {
            body.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, String(nextIndex)));
            nextIndex += 1;
            recalculate();
        });

        body.addEventListener('input', recalculate);
        body.addEventListener('change', recalculate);
        body.addEventListener('click', function (event) {
            var button = event.target.closest('.remove-line');

            if (button) {
                button.closest('.order-line').remove();
                recalculate();
            }
        });

        recalculate();
    }
})();
