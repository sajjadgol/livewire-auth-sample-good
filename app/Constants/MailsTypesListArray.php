<?php

namespace App\Constants;

class MailsTypesListArray
{
    const DEFAULT = [
        MailsVarables::HEADER,
        MailsVarables::FOOTER,
        MailsVarables::SITE_URL,
        MailsVarables::SITE_LOGO,
        MailsVarables::SITE_NAME,
        MailsVarables::SITE_ADMIN_EMAIL,
    ];
    const TypeArray = [
        [
            "id" => 1,
            "name" => "Forget Password For testing",
            "code" => "forget_password",
            "subject" => "Hi {{name}}, please verify your {{site_name}} account",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME]
        ],
        [
            "id" => 2,
            "name" => "Mobile number updation request",
            'code' => 'mobile_number_update',
            "subject" => "Hi {{site_name}}, please update mobile nunber request {{name}} account",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER]
        ],
        [
            "id" => 3,
            "name" => "Order driver assign request update status",
            'code' => 'order_assign_pending_request',
            "subject" => "Hi {{site_name}}, please update mobile number request {{name}} account",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER]
        ],
        [
            "id" => 4,
            "name" => "Email updation request",
            'code' => 'email_request_update',
            "subject" => "Hi {{site_name}}, please update mobile nunber request {{name}} account",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER,MailsVarables::EMAIL,MailsVarables::VERIFICATION_CODE]
        ],
        [
            "id" => 5,
            "name" => "New Account Request",
            'code' => 'new_account_request_update',
            "subject" => "Hi {{site_name}}, please approve request {{name}} account",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER,MailsVarables::EMAIL,MailsVarables::VERIFICATION_CODE]
        ],
        [
            "id" => 6,
            "name" => "New Message",
            'code' => 'new_message_request',
            "subject" => "Hi {{site_name}}, please check massage from  {{name}} account",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER,MailsVarables::EMAIL,MailsVarables::MSG_TITLE,MailsVarables::MSG_BODY]
        ],
        [
            "id" => 7,
            "name" => "Order Status Updated",
            'code' => 'order_status_update',
            "subject" => "Hi {{name}}, Your order status have been updated successfully, Your status is ({{status}}).",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER,MailsVarables::EMAIL,MailsVarables::ORDER_NUMBER,MailsVarables::STATUS,MailsVarables::STORE_NAME]
        ],
        [
            "id" => 8,
            "name" => "Order Delivered Successfully",
            'code' => 'order_delivery_update',
            "subject" => "Hi {{name}}, Your Order #{{ order_number }} have been delivered successfully.",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER,MailsVarables::EMAIL,MailsVarables::ORDER_NUMBER,MailsVarables::STATUS,MailsVarables::STORE_NAME]
        ],
        [
            "id" => 9,
            "name" => "Account Suspended",
            'code' => 'user_account_suspend',
            "subject" => "Hi {{name}}, Your account have been suspended, please contact to administrator.",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER,MailsVarables::EMAIL]
        ],
        [
            "id" => 9,
            "name" => "Order Request , Driver not found",
            'code' => 'order_send_to_admin',
            "subject" => "Hi , Order Number : {{order_number}} , Driver not found ,plase assign order to driver",
            'default' => self::DEFAULT,
            "varables" => [MailsVarables::NAME,MailsVarables::NUMBER,MailsVarables::EMAIL,MailsVarables::ORDER_NUMBER,MailsVarables::STATUS,MailsVarables::STORE_NAME]
        ]
    ];
}
