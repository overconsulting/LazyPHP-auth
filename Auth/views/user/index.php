<?php $url = $user->media != null ? $user->media->getUrl() : '';
    if ($url != '') {
        // $avatar = '{% image src="'.$url.'" width="150px" height="150px" %}';
        $avatar = '<img src="'.$url.'"  class="rounded-circle" />';
    } else {
        $avatar = '<i class="fa fa-user"></i>';
    }
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
			<h1 class="page-title">Votre compte</h1>
			<div class="box box-success">

			    <div class="box-infos">
					<h3>Informations personnelles</h3>
		            <div class="card slot-coach">
		                    <div>
		                        <?php echo $avatar; ?>
		                    </div>

		                    <div>
		                        <strong><?php echo $user->fullname ; ?></strong>
		                        <br /><?php echo $user->email; ?> 
		                    </div>
		                </div>
		            </div>     
		         </div>

				<div class="box-orders">
					<h3>Commandes</h3>
		            <div class="card">
		             <table class="table table-hover table-sm ">
            <thead>
                <tr>
                    <th>Coach</th>
                    <th>Catégorie</th>
                    <th>Activité - Date</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th width="15%"></th>
                </tr>
            </thead>
            <tbody>
						<?php
						foreach ($orders as $order) {
		                             foreach ($order->orderdetails as $orderdetail) {
		                                 $label = $orderdetail->product->label;
		                                 $label_slot = $orderdetail->product->label_slot;
		                                 $amount = $orderdetail->amount;
		                                 $quantity = $orderdetail->quantity ;
		                                        }  

		                                        //A supprimer quand le if fonctionnera
		                                        $removebutton='{% button url="userauth_removeorder_' . $order->id .'" type="danger" size="sm" icon="trash-o" confirmation="Vous confirmer vouloir annuler cette commande ?" hint="Supprimer" %}';

		                                        //Condition : si la date > à Current date +1, on affiche pas le btn
		                                        /*if(){
		                                        	$removebutton='{% button url="userauth_removeorder_<?php echo $order->id; ?>" type="danger" size="sm" icon="trash-o" confirmation="Vous confirmer vouloir annuler cette commande ?" hint="Supprimer" %}';
		                                        } else {
		                                        	$removebutton='';
		                                        }*/

		                                        if($order->status == 'waiting' or $order->status == 'paid' ){
		                                        	echo
						                                '<div class="slot-order">
						                                
							                                <tr>
							                                    <td>'. $order->user->fullname . /* nom du coach */'</td> 
							                                    <td>'. $label_slot ./* Sous catégorie */'</td>
							                                    <td>'. $label ./* catégorie */'</td>
							                                    <td>'. number_format(round($amount, 2),2) . /* Prix */' euros </td>  
							                                    <td>'. $quantity . /* quantité */ '</td>     
								                                <td>' . $removebutton . '</td>'.
							                                '</tr> 
						                                <br /></div>';   
		                                        }
		                                
		                        }
						?>
					</div>
				 </div>

				 </tbody>
        </table>

			 	<div class="box-body">
			        {% link url="user_edit" content="Modifier vos données personnelles" icon="pencil" %}<br />
					{% link url="auth_logout" content="Se déconnecter" icon="sign-out" %}
				</div>

			</div>
		</div>
	</div>
</div>
