{layout '../Public/sideForm.latte'}
{block title}新增角色{/block}

{block content}
    <div class="cbox">
        <form id="formArticle" class="stdform mform" method="post" action="">
            <table cellpadding="0" cellspacing="0" border="0" id="frmtable" class="formtable">
                <thead>
                <tr>
                    <td><label>角色名称</label></td>
                    <td>
                        <div class="control-group ">
                            <div class="input-group">
                                <input type="text" name="names" class="smallinput"/>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><label>角色标识</label></td>
                    <td>
                        <div class="control-group ">
                            <div class="input-group">
                                <input type="text" name="roleName" class="smallinput"/>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><label>排序</label></td>
                    <td>
                        <div class="control-group ">
                            <div class="input-group">
                                <input type="text" name="sort" class="smallinput" value="0"/>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><label>状态</label></td>
                    <td>
                        <div class="field control-group">
                            <div class="input-group">
                                <label class='radio'><input name='status' checked type="radio" value='1'>启用</label>
                                <label class='radio'><input name='status' type="radio" value='0'>停用</label>
                            </div>
                        </div>
                    </td>
                </tr>
                </thead>
            </table>
        </form>
{/block}