<?php

//	Good

	switch ($two) { 
		case 1: 
			$a = $b; 
			break; 
		case 2: 
			$c = $b; 
			break; 
		default: 
			handle_default_case(); 
			break; 
	} 

//	Good

	switch ($two) { 
		case 1: 
			do_something(); 
			break; 
		case 2: 
			do_something_different(); 
			break; 
		default: 
			handle_default_case(); 
			break; 
	} 

//	Good

	switch ($two)
	{ 
		case 1: 
			do_something(); 
			break; 
		case 2: 
			do_something_different(); 
			break; 
		default: 
			handle_default_case(); 
			break; 
	} 

//	Good

	switch ($two) { 
		case 1: 
		case 2: 
			do_something_different(); 
			break; 
		default: 
			handle_default_case(); 
			break; 
	} 

//	Good

	switch ($two) { 
		case 1: 
			do_something(); 
		case 2: 
			do_something_different(); 
			break; 
		default: 
			handle_default_case(); 
			break; 
	} 

//	Good

	switch ($two) { 
		case 1: 
			do_something(); 
			break; 
		case 2: 
			do_something_different(); 
			break; 
		default: 
			handle_default_case(); 
	} 

?>