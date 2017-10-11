<?php
/*
 * BP Profile Search - filters template 'bps-filters'
 *
 * See http://dontdream.it/bp-profile-search/form-templates/ if you wish to modify this template or develop a new one.
 *
 */

	$F = bps_escaped_filters_data ();
	if (empty ($F->fields))  return false;
?>
	<p class='bps_filters'>

<?php
	foreach ($F->fields as $f)
	{
		$filter = _bps_print_filter ($f);
		$filter = apply_filters ('bps_print_filter', $filter, $f);

?>
		<strong><?php echo $f->label; ?></strong> <span><?php echo $filter; ?></span><br>
<?php
	}
?>
		<a href='<?php echo $F->action; ?>'><?php _e('Clear', 'buddypress'); ?></a>
	</p>
<?php

function _bps_print_filter ($f)
{
	$filters = array ();

	$filters['']			= __('is', 'bp-profile-search');
	$filters['contains']	= __('contains', 'bp-profile-search');
	$filters['like']		= __('is like', 'bp-profile-search');
	$filters['match']		= __('match', 'bp-profile-search');
	$filters['match_all']	= __('match all', 'bp-profile-search');
	$filters['match_any']	= __('match any', 'bp-profile-search');
	$filters['max']			= __('max', 'bp-profile-search');
	$filters['min']			= __('min', 'bp-profile-search');
	$filters['one_of']		= __('is one of', 'bp-profile-search');

	if (count ($f->options))
	{
		$values = array ();
		foreach ($f->options as $key => $label)
			if (in_array ($key, $f->values))  $values[] = $label;
	}

	switch ($f->filter)
	{
	case 'range':
	case 'age_range':
		$filter = array ();
		if (isset ($f->value['min']))  $filter[] = $filters['min']. ': '. $f->value['min'];
		if (isset ($f->value['max']))  $filter[] = $filters['max']. ': '. $f->value['max'];
		return implode (', ', $filter);

	case '':
		if (isset ($values))
			return $filters[$f->filter]. ': '. $values[0];
	case 'contains':
	case 'like':
		return $filters[$f->filter]. ': '. $f->value;

	case 'one_of':
		if (count ($values) == 1)
			return $filters['']. ': '. $values[0];
		return $filters[$f->filter]. ': '. implode (', ', $values);

	case 'match_any':
	case 'match_all':
		if (count ($values) == 1)
			return $filters['match']. ': '. $values[0];
		return $filters[$f->filter]. ': '. implode (', ', $values);

	default:
		return "BP Profile Search: undefined filter <em>$f->filter</em>";
	}
}

// BP Profile Search - end of template
