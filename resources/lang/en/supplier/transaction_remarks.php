<?php

return [
    'SUPPLIER_COMMISSION'=>['DEBIT'=>'', 'CREDIT'=>''],
    'SUPPLIER_SELLS'=>['DEBIT'=>'for supplier sales payment of :amount', 'CREDIT'=>'for the sales of :amount'],
    'SUPPLIER_SELLS_TAX'=>['DEBIT'=>'for the sales tax of :amount', 'CREDIT'=>'for the supplier sales tax of :amount'],
    'PARTNER_MARGIN_PAYMENT'=>['DEBIT'=>'for partner sales margin payment', 'CREDIT'=>'for sales margin'],
    'PARTNER_MARGIN_TAX'=>['DEBIT'=>'for sales margin tax of :amount', 'CREDIT'=>'for partner sales margin tax of :amount'],
    'PARTNER_COMMISSION'=>['DEBIT'=>'for partner sales commission', 'CREDIT'=>'for sales commission'],
    'ORDER_PAYMENT'=>['DEBIT'=>'', 'CREDIT'=>''],
    'SUB_ORDER_PAYMENT'=>['DEBIT'=>'', 'CREDIT'=>''],
    'ORDER_ITEM_PAYMENT'=>['DEBIT'=>'for the purchase', 'CREDIT'=>'purchase payment'],
    'PACKAGE_PUR'=>'',
    'RENEW_PACKAGE'=>'',
    'CURRENCY_CONVERSION'=>[
        'DEBIT'=>':from_amount debited from your account to convert into :to_amount with the rate of :rate',
        'CREDIT'=>':to_amount creditted by converting :from_amount with the rate of :rate'
    ],
    'WITHDRAW'=>['DEBIT'=>'for withdrawal for :amount', 'CREDIT'=>'for withdrawal for :amount'],
    'WITHDRAWAL_CHARGES'=>['DEBIT'=>'', 'CREDIT'=>''],
    'WITHDRAW_CANCEL'=>['DEBIT'=>'', 'CREDIT'=>''],
    'WITHDRAWAL_PAYMENT'=>['DEBIT'=>'fot the withdrawal request', 'CREDIT'=>'fot the withdrawal request'],
    'ORDER_ITEM_CANCEL_REFUND'=>['DEBIT'=>'for the cancellation of paid order', 'CREDIT'=>'for the cancellation of paid order']
];
