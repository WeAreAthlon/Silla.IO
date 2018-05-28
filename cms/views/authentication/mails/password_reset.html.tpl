{$_labels.mails.reset.greeting} <b>{$name}</b>,
<br>
{$_labels.mails.reset.reason}: <a href="{$_config->urls('full')}">{$_labels.title}({$_labels.client})</a>.<br>
{$_labels.mails.reset.instructions}: <a href="{$password_reset_link}">{$password_reset_link}</a><br>
<hr>
<i>{$_labels.mails.reset.note}</i>
<br>
{$_labels.client}