<?php
	function StringEndsWith($string, $suffix)
	{
		$length = strlen($suffix);
		if ($length == 0) 
		{
			return true;
		}
		return (substr($string, -$length) === $suffix);
	}

	function FormatHeader($header)
	{
		$header = RemoveFileExtension($header);
		$header = InsertSpaceBetweenLettersAndNumbers($header);
		$header = InsertSpaceBeforeCapitals($header);
		$header = ucwords($header);
		return $header;
	}
	
	function RemoveFileExtension($text)
	{
		return preg_replace("/(\.[a-z]+)/i","", $text);
	}
	
	function InsertSpaceBetweenLettersAndNumbers($text)
	{
		return preg_replace("/([a-z]+)([0-9]+)/i","$1 $2", $text);
	}
	
	function InsertSpaceBeforeCapitals($text)
	{
		return preg_replace("/([a-z]+)([A-Z]+)/","$1 $2", $text);
	}

	function EncodeURI($text)
	{
		$text = str_replace("#", "%23", $text);
		$text = str_replace(".", "%2E", $text);
		$text = str_replace("(", "%28", $text);
		$text = str_replace(")", "%29", $text);
		$text = str_replace(" ", "%20", $text);
		return $text;
	}
	
	function HTMLEncodeSpecialCharacters($line)
	{
		$line = str_replace("<", "&lt;", $line);
		$line = str_replace(">", "&gt;", $line);
		return $line;
	}
	
	function IsTitle($line)
	{
		return (strlen($line) > 2 && substr($line, 0, 3) == "###");
	}

	function IsHeader($line)
	{
		return (!IsTitle($line) && strlen($line) > 1 && substr($line, 0, 2) == "##"); //Markdown header format
	}

	function IsSubHeader($line)
	{
		return (!IsTitle($line) && !IsHeader($line) && strlen($line) > 0 && substr($line, 0, 1) == "#"); //Markdown subheader format
	}
	
	function IsImage($line)
	{
		return (strlen($line) > 2 && substr($line, 0, 2) == "!["); //Markdown image format ![alt text](address)
	}
	
	function IsHyperlink($line)
	{
		return (strlen($line) > 3 && substr($line, 0, 3) == "!!["); //codenotes hyperlink format !![text](address) must be on its own line
	}
	
	function EndsWith($text, $end)
	{
		if(strlen($text) < strlen($end)) return false;
		return (substr($text, strlen($text) - strlen($end)) == $end);
	}
	
	$insideBigTag = false;
	function DisplayLine($line)
	{
		global $insideBigTag;

		if(IsBigTag($line))
		{
			$insideBigTag = (!$insideBigTag);
			echo $line;
			return;
		}

		$line = HTMLEncodeSpecialCharacters($line);
		$line = CheckForInlineComments($line);
		$line = CheckForMultilineComments($line);
		$line = CheckForTabs($line);
		echo $line;
		echo "<br/>";
	}
	
	function CheckForInlineComments($line)
	{
		$commentsStart = strpos($line, "--");
		if($commentsStart != FALSE)
		{
			$line = substr_replace($line, "<span class='comments'>", $commentsStart, 0);
			$line = $line."</span>";
		}
		$commentsStart = strpos($line, "//");
		if($commentsStart != FALSE)
		{
			$line = substr_replace($line, "<span class='comments'>", $commentsStart, 0);
			$line = $line."</span>";
		}
		return $line;
	}
	
	function CheckForMultilineComments($line)
	{
		$commentsStart = strpos($line, "/*");
		$commentsEnd = strrpos($line, "*/");
		if($commentsStart == FALSE && $commentsEnd == FALSE)
		{
			return $line;
		}
		if($commentsStart == FALSE)
		{
			$commentsStart = 0;
		}
		if($commentsEnd == FALSE)
		{
			$commentsEnd = strlen($line)-2;
		}	
		$line = substr_replace($line, "</span>", $commentsEnd+2, 0);
		$line = substr_replace($line, "<span class='comments'>", $commentsStart, 0);
		return $line;
	}
	
	function CheckForTabs($line)
	{
		return preg_replace("/\t/","&nbsp;&nbsp;&nbsp;&nbsp;", $line);
	}
	
	function IsBigTag($text)
	{
		return (IsOpenBigTag($text) || IsCloseBigTag($text));
	}
	
	function IsOpenBigTag($text)
	{
		return (preg_match("/^(\t|\s)*<big.*>\s*$/", $text) == 1);
	}
	
	function IsCloseBigTag($text)
	{
		return (preg_match("/^(\t|\s)*<\/big.*>\s*$/", $text) == 1);
	}
	
?>