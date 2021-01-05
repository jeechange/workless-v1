{layout '../Public/layoutList.latte'} {*继承layoutList模板*}
{block title}角色管理{/block}
{block actions}<a href="{url('consoles_add','con=Roles')}" class="icon-adds" data-side-form="">新增角色</a>{/block}

{*选中记录的操作按钮*}
{block actions2}
    <div class="list-more-action-item" data-url="{url('consoles_delete_mul','con=Roles')}">删除项目</div>
{/block}
{block content}
    <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
        <thead>
        <tr>
            <th></th>
            <th data-sort="r.names"><span class="table-head">角色名称</span></th>
            <th data-sort="r.roleName"><span class="table-head">角色标识</span></th>
            <th data-sort="r.weight"><span class="table-head">角色类型</span></th>
            <th data-sort="r.sort"><span class="table-head">排序</span></th>
            <th data-sort="r.status"><span class="table-head">状态</span></th>
            <th><span class="table-head">操作</span></th>
        </tr>
        </thead>
        <tbody>
        {foreach $lists as $item }
            <tr>
                <td style="width: 60px;"><input type="checkbox" name='ids[]' class="checkall_1 checkall-item" value="{$item['id']}"/></td>
                <td>{$item['names']}</td>
                <td>{$item['roleName']}</td>
                <td>{$item['weight']>0?"内置":"自定义"}</td>
                <td>{$item['sort']}</td>
                <td>{$item['statusMemo']}</td>
                <td>
                    {if $item['status']==0}
                        <a href="{url('consoles_roles_states',array('id'=>$item['id'],"status"=>1))}">启用</a>
                    {else}
                        <a href="{url('consoles_roles_states',array('id'=>$item['id'],"status"=>0))}">停用</a>
                    {/if}
                    |<a href="{url('consoles_mod','con=Roles&id='.$item['id'])}">修改</a>
                    {if $item['weight']==0} |
                        <a data-confirm="确认删除吗？" href="{url('consoles_delete','con=Roles&id='.$item['id'])}">删除</a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/block}