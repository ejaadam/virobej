<?php

return ['validation'=>[
               'supplier_product_new.store_id.required'=>'Please select Store',
            'product.category_id.required'=>'Please select Catergory',
            'product.brand_id.required'=>'Please select Brand',
            'product.product_name.required'=>'Please enter Product Name',
            'product.sku.required'=>'Please enter SKU',
            'product.description.required'=>'Please enter Description',
            'supplier_product_new.currency_id.required'=>'Please select Currency',
            'supplier_product_new.mrp_price.required'=>'Please enter MRP Price',
            'supplier_product_new.price.required'=>'Please enter Price',
           
],
    'validation_ext_product'=>[
            'supplier_product.store_id.required'=>'Please select Store',
            'brand_id.required'=>'Please select Brand',
            'category_id.required'=>'Please select Catergory',
            'supplier_product.product_id.required'=>'Please select Product',
           // 'supplier_product_new.currency_id.required'=>'Please select Curreny',
            'supplier_product.mrp_price.required'=>'Please enter MRP Price',
            'supplier_product.price.required'=>'Please enter the price',
            'supplier_product.pre_order.required'=>'Please select Pre Order', 
    ]    
    ];




