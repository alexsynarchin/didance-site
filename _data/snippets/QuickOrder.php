id: 65
source: 1
name: QuickOrder
category: pdoTools
properties: 'a:0:{}'

-----

$errors = array();

if ( empty( trim( $_POST['total'] ) ) OR !is_numeric( trim( $_POST['total'] ) ) ) {
    $errors['total'] = 'Вы не заполнили кол-во человек';
}

if ( empty( trim( $_POST['phone'] ) ) ) {
    $errors['phone'] = 'Вы не заполнили телефон';
}

if ( !empty( $errors ) ) {
    return $AjaxForm->error( 'В форме содержатся ошибки!', $errors );
} else {
    $miniShop2 = $modx->getService( 'minishop2','miniShop2', MODX_CORE_PATH . 'components/minishop2/model/minishop2/', $scriptProperties );

    if ( !( $miniShop2 instanceof miniShop2 ) ) {
        return $AjaxForm->error( 'Ошибка скрипта!' );
    }
    
    $miniShop2->initialize($modx->context->key, $scriptProperties);
    
    $miniShop2->order->add( 'receiver', trim( $_POST['receiver'] )  );
    $miniShop2->order->add( 'email', trim( $_POST['email'] ) );
    $miniShop2->order->add( 'delivery', 1 ); // id метода доставки
    $miniShop2->order->add( 'payment', 1 ); // id метода оплаты
    
    $miniShop2->cart->add($_POST['id'], $modx->getOption('count', $_POST, 1), $modx->getOption('options', $_POST, array()));
    
    return $AjaxForm->success( 'Форма успешно отправлена. Сейчас вы перейдете на страницу оплаты.' );
}