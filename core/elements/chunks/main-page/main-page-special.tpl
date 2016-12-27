<section class="special-block">
    <div class="special-block__background-image" ></div>
    <h3 class="block-heading special-block__heading">Начать танцевать – просто!</h3>
    <div class="container">
        <div  class="row">
            <div class="special-block__content">
                <p> Для начала достаточно отправить нам заявку. Мы перезвоним и проконсультируем по всем вопросам!</p>
            </div>
            {$_modx->runSnippet('!AjaxForm@PropertySet',[
            'snippet' => 'FormIt',
            'form' => '@FILE chunks/forms/main-page__special-form.tpl',
            'hooks' => 'email',
            'emailSubject' => 'Заявка c главной страницы | Спецпредложение',
            'emailTo' => 'gwynbleid11@yandex.ru,didance.ru@gmail.com',
            'emailTpl' => 'SentOrderTpl',
            'validate' => 'name:required, phone:required',
            'validationErrorMessage' =>'В форме содержатся ошибки!',
            'successMessage'=>'Благодарим за обращение, мы свяжемся с вами в близжайшее время.'
            ])}
        </div>
    </div>
</section>