<div class="jcommunity-box jcommunity-account">
<h1>Profil de {$user->login|eschtml}</h1>

<table>
<tr>
    <td>Pseudonyme</td> <td>{$user->nickname|eschtml}</td>
</tr>
{ifuserconnected}
<tr>
    <td>Email</td> <td>{$user->email|eschtml}</td>
</tr>
{/ifuserconnected}
</table>

{if $himself}
<ul>
    <li><a href="{jurl 'jcommunity~account:prepareedit', array('user'=>$user->login)}">Editer votre profil</a></li>
    <li><a href="{jurl 'jcommunity~account:destroy', array('user'=>$user->login)}">Effacer votre profil</a></li>
</ul>
{/if}
</div>