<!--Callback modal-->
<div class="modal-vcenter modal fade" id="CallBackModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="text-center modal-title" id="myModalLabel">Заказ обратного звонка</h3>
            </div>
            <div class="modal-body">
                {$_modx->runSnippet('!AjaxForm@PropertySet',[
                'snippet' => 'FormIt',
                'form' => '@FILE chunks/forms/base__callback-form.tpl',
                'hooks' => 'email',
                'emailSubject' => 'Заявка c сайта didance | Обратный звонок',
                'emailTo' => 'gwynbleid11@yandex.ru,didance.ru@gmail.com',
                'emailTpl' => 'SentCallBackTpl',
                'validate' => 'name:required, phone:required',
                'validationErrorMessage' =>'В форме содержатся ошибки!',
                'successMessage'=>'Благодарим за обращение, мы свяжемся с вами в близжайшее время.'
                ])}
            </div>
        </div>
    </div>
</div>
<!--End of Callback modal-->