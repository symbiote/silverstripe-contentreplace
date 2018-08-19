<% if $File %>
	<% with $File %>
		$Up.LinkHTML.RAW
		<strong>($Extension.UpperCase)</strong>
		<strong>$Size</strong>
	<% end_with %>
<% end_if %>