<h1 class="page-title">{{ pageTitle }}</h1>
<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">{{ boxTitle }}</h3>
        <div class="box-tools pull-right">
            {% button url="cockpit_auth_users_new" type="success" size="sm" icon="plus" hint="Ajouter" %}
            {% button url="cockpit_auth_users_exportcsv" type="info" size="sm" icon="upload" hint="Exporter" %}
            <?php if ($this->current_user !== null && $this->current_user->group->code == 'administrators'): ?>
                {% button url="cockpit_auth_users_importcsv" type="warning" size="sm" icon="download" hint="Importer" %}
            <?php endif; ?>
        </div>
    </div>
    <div class="box-body">
        <table class="data-table table table-hover table-sm">
            <thead>
                <tr>
                    <th width="1%">ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Groupe</th>
                    <th>Actif</th>
                    <th width="15%">Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
foreach ($params['users'] as $user) {
    if ($user->active == 1) {
        $active = '<span class="label label-success">Activé</span>';
    } else {
        $active = '<span class="label label-danger">Désactivé</span>';
    }

    if ($user->media_id !== null && $user->media !== null) {
        $avatar = '<img class="user-avatar" src="'.$user->media->getUrl().'" />&nbsp;';
    } else {
        $avatar = '';
    }

    $classAssignGroup = $this->loadModel('GroupAssignment');
    $groupsAssigns = $classAssignGroup::findByUser($user->id);
    
    $groups = [];
    foreach ($groupsAssigns as $groupsAssig) {
        $groups[] = $groupsAssig->group->label;
    }

    echo
        '<tr>'.
            '<td>'.$user->id.'</td>'.
            '<td>'.$avatar.$user->getFullName().'</td>'.
            '<td>'.$user->email.'</td>'.
            '<td>'.($user->group_id != null ? $user->group->label : '').'</td>'.
            '<td>';
            foreach ($groups as $group) {
                echo $group . ", ";
            }
            echo '</td>'.
            '<td>'.$active.'</td>'.
            '<td>';?>
            {% button url="cockpit_auth_users_sendnewpassword_<?php echo $user->id; ?>" type="warning" size="sm" icon="key" hint="Renvoyer le mot de passe" %}
            {% button url="cockpit_auth_users_edit_<?php echo $user->id; ?>" type="info" size="sm" icon="pencil" hint="modifier" %}
            {% button url="cockpit_auth_users_delete_<?php echo $user->id; ?>" type="danger" size="sm" icon="trash-o" confirmation="Vous confirmer vouloir supprimer cet utilisateur ?" hint="Supprimer" %}
<?php
    echo
            '</td>'.
        '</tr>';
}
?>
            </tbody>
        </table>
    </div>
</div>