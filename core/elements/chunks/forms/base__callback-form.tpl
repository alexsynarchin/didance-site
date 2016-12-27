<form class="form-horizontal ajax_form" action="" method="post" {ignore}onsubmit="yaCounter34372255.reachGoal('Target_CallBack', function (){console.log('запрос в Метрику успешно отправлен');}); return true;"{/ignore}>
    <div class="form-group">
        <div class="col-xs-6">
            <div class="input-group input-group-lg">
                <span class="input-group-addon"><i class="icon el el-user"></i></span>
                <input type="text" class="form-control" name="name"  placeholder="Как вас зовут" value="[[+fi.name]]" >
            </div>
        </div>
        <div class="col-xs-6">
            <div class="input-group input-group-lg">
                <span class="input-group-addon" ><i class="icon el el-phone"></i></span>
                <input type="text" class="form-control" name="phone"  placeholder="Ваш телефон" value="[[+fi.phone]]">
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-didance btn-lg">Перезвоните мне</button>
    </div>
</form>