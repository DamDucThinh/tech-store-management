<?php

/*
| Câu báo lỗi validation tiếng Việt cho các rule dùng trong project.
| Rule nào không có ở đây sẽ dùng câu tiếng Anh mặc định của Laravel.
*/

return [

    'array' => ':Attribute phải là một danh sách.',
    'boolean' => ':Attribute chỉ nhận giá trị đúng hoặc sai.',
    'confirmed' => 'Xác nhận :attribute không khớp.',
    'distinct' => ':Attribute bị trùng.',
    'email' => ':Attribute phải là địa chỉ email hợp lệ.',
    'enum' => ':Attribute đã chọn không hợp lệ.',
    'exists' => ':Attribute đã chọn không tồn tại.',
    'image' => ':Attribute phải là file ảnh.',
    'in' => ':Attribute đã chọn không hợp lệ.',
    'integer' => ':Attribute phải là số nguyên.',
    'max' => [
        'array' => ':Attribute không được quá :max mục.',
        'file' => ':Attribute không được lớn hơn :max KB.',
        'numeric' => ':Attribute không được lớn hơn :max.',
        'string' => ':Attribute không được dài quá :max ký tự.',
    ],
    'mimes' => ':Attribute phải có định dạng: :values.',
    'min' => [
        'array' => ':Attribute phải có ít nhất :min mục.',
        'numeric' => ':Attribute phải lớn hơn hoặc bằng :min.',
        'string' => ':Attribute phải có ít nhất :min ký tự.',
    ],
    'numeric' => ':Attribute phải là số.',
    'regex' => ':Attribute không đúng định dạng.',
    'required' => 'Vui lòng nhập :attribute.',
    'string' => ':Attribute phải là chuỗi ký tự.',
    'unique' => ':Attribute đã tồn tại.',

    'attributes' => [
        'name' => 'tên',
        'description' => 'mô tả',
        'category_id' => 'danh mục',
        'price' => 'giá',
        'image' => 'hình ảnh',
        'status' => 'trạng thái',
        'username' => 'tên đăng nhập',
        'password' => 'mật khẩu',
        'role' => 'vai trò',
        'email' => 'email',
        'phone' => 'số điện thoại',
        'address' => 'địa chỉ',
        'avatar' => 'ảnh đại diện',
        'customer_name' => 'tên khách hàng',
        'customer_phone' => 'số điện thoại khách hàng',
        'items' => 'sản phẩm',
    ],

];
