{layout '../Public/layoutForm.latte'}
{block return_actions}{/block}
{block title}权限管理{/block}
{block private_css}
    <link href="{path('[Jeechange]/metroUI/css/supplyrole.css')}" rel="stylesheet"/>
    <style>
        .node-list{
            padding-left:120px;
            box-sizing:content-box;
            min-height:30px;
        }

        .node-list .theme{
            width:120px;
            text-align:left;
        }

        .node-list .theme label{
            margin-left:0;
        }

        .node-list label{
            vertical-align:middle;
        }
    </style>
{/block}
{block private_js}
    <script type="text/javascript">
        $(function () {
            $("#roleId").on("change", function () {
                var href = $(this).attr("href");
                var roleId = $(this).val();

                getPage(href + "?roleId=" + roleId);
                return false;
            });
        });

    </script>
{/block}
{block content}
    <form class="mform" method="post" action="{url('consoles_roles_accessModify')}">
        <table cellpadding="0" cellspacing="0" border="0" id="frmtable" class="formtable">
            <thead>
            <tr>
                <td><label>角色名称</label></td>
                <td>
                    <div class="control-group">
                        <div class="input-group">
                            <select name="roleId" id="roleId" style="width: 180px;height: 30px;" href="{url('consoles_roles_rolesAccess')}">
                                {foreach $roles as $role}
                                    <option value="{$role["id"]}" {if $role["id"]==$roleId}selected{/if}>{$role["names"]}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label>权限明细</label></td>
                <td style=" vertical-align: middle;">
                    {foreach $menus as $nodeid=>$menu}
                        <ul class="node-list">
                            <li class="theme">
                                <label for="nodelist_{$menu["id"]}">
                                    <input
                                            id="nodelist_{$menu["id"]}"
                                            type="checkbox" name="menus[]"
                                            class="node-item"
                                            {if in_array($menu["id"],$accessList)}checked{/if}
                                            value="{$menu["id"]}"
                                    />
                                    {$menu["names"]}
                                </label>
                            </li>
                            {foreach $menu["sub"] as $smenu}
                                <li class="item">
                                    <label for="nodelist_{$smenu["id"]}">
                                        <input
                                                id="nodelist_{$smenu["id"]}"
                                                type="checkbox" name="menus[]"
                                                class="node-item"
                                                {if in_array($smenu["id"],$accessList)}checked{/if}
                                                value="{$smenu["id"]}"
                                        />
                                        {$smenu["names"]}
                                    </label>
                                </li>
                            {/foreach}
                        </ul>
                    {/foreach}

                </td>
            </tr>
            </thead>
        </table>
    </form>
{/block}