<section class="utp">
    <div class="utp__image"></div>
    <div class="utp__background-gradient"></div>
    <div class="container">
        <div class="row">
            <div class="utp__heading">
                <h1 class="utp__heading-first">
                    Студия танца для взрослых и детей
                </h1>
                <h2 class="utp__heading-second">Парные и сольные  направления, латиноамериканские и уличные стили.</h2>
            </div>
            <div class="utp__panel">
                <h3 class="utp__panel-heading">
                    Запишитесь на свое первое занятие сейчас. Такие решения нельзя откладывать на потом
                </h3>
                {$_modx->runSnippet('!AjaxForm@PropertySet',[
                    'snippet' => 'FormIt',
                    'form' => '@FILE chunks/forms/main-page__utp-form.tpl',
                    'hooks' => 'email',
                    'emailSubject' => 'Заявка c главной страницы | Первый экран',
                    'emailTo' => 'gwynbleid11@yandex.ru,didance.ru@gmail.com',
                    'emailTpl' => 'SentOrderTpl',
                    'validate' => 'name:required, phone:required',
                    'validationErrorMessage' =>'В форме содержатся ошибки!',
                    'successMessage'=>'Благодарим за обращение, мы свяжемся с вами в близжайшее время.'
                ])}
            </div>
        </div>
    </div>
</section>