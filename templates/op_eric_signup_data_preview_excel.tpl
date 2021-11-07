<h2 class="my">匯入「<{$action.title}>」報名資料預覽</h2>
<!-- {*本次匯入筆數，需扣標題列，因smarty關係，每次只能處關係，每次只能處理簡單運算，所以需分2列處理*} -->
<{assign var=import_number value=$preview_data|@count}>
<{assign var=import_number value=$import_number-1}>

<div class="alert alert-<{if $import_number+$action.signup_count>$action.number+$action.condidate}>danger<{else}>sucess<{/if}>">
    可報名數：<{$action.number}> 人，
    可侯補數：<{$action.condidate}> 人，
    已報名數：<{$action.signup_count}> 人，
    欲匯入數：<{$import_number}> 人
</div>
<form action="index.php" method="post" id="myForm"  >
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <{foreach from=$head item=title }>
                <th><{$title}></th>
                <{/foreach}>
            </tr>
        </thead>
        <tbody>
            <{foreach from=$preview_data key=i item=data name=preview_data}>
                <{if $smarty.foreach.preview_data.iteration>1}> <!--{*從第2列開始取 *} -->
                    <tr>
                        <{foreach from=$data key=j item=val}>
                            <{assign var=title value=$head.$j}>
                            <{assign var=input_type value=$type.$j}>
                            <{assign var=input_options value=$options.$j}><!--{* checkbox預設選項 *} -->
                            <{if $title!=''}>
                                <td>

                                    <!--{* checkbox *} -->
                                    <{if $input_type=="checkbox"}>
                                        <{assign var=var_arr value='|'|explode:$val}>
                                        <{foreach from=$input_options item=opt}> <!--{* 取checkbox預設選項 *} -->

                                            <div class="form-check-inline checkbox-inline">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" name="tdc[<{$i}>][<{$title}>][]" value="<{$opt}>" <{if $opt|in_array:$var_arr}>checked<{/if}>><{$opt}>  <!--{* 如果預設選項出現在var_arr則顯示checked *} -->
                                                </label>
                                            </div>
                                        <{/foreach}>

                                    <!--{* radio *} -->
                                    <{elseif $input_type=="radio"}>
                                        <{foreach from=$input_options item=opt}> <!--{* 取radio預設選項 *} -->

                                            <div class="form-check-inline radio-inline">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="radio" name="tdc[<{$i}>][<{$title}>]" value="<{$opt}>" <{if $opt==$val}>checked<{/if}>><{$opt}>  <!--{* checkbox要多一層[] radio和select 不用 *} -->
                                                </label>
                                            </div>
                                        <{/foreach}>

                                    <!--{* select *} -->
                                    <{elseif $input_type=="select"}>

                                        <select name="tdc[<{$i}>][<{$title}>]"  class="form-control validate[required]" >
                                            <{foreach from=$input_options item=opt}>
                                                <option value="<{$opt}>" <{if $opt==$val}>selected<{/if}>><{$opt}></option>
                                            <{/foreach}>
                                        </select>

                                    <{else}>
                                        <input type="text" name="tdc[<{$i}>][<{$title}>]" value="<{$val}>" class="form-control form-control-sm">
                                    <{/if}>

                                </td>
                            <{/if}>
                        <{/foreach}>

                    </tr>

                <{/if}>
            <{/foreach}>
    </table>
    <{$token_form}>
    <input type="hidden" name="id" value="<{$action.id}>">
    <input type="hidden" name="op" value="eric_signup_data_import_excel">
    <div class="bar">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save" aria-hidden="true"></i> 匯入EXCEL資料
        </button>
    </div>
</form>
