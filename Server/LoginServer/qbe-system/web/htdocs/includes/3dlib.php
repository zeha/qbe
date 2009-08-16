<?
//****************************************************************************
// php3DLib 0.11
//****************************************************************************
//      Author: Maxx 1,5k 
//	             a.k.a. bitkid 
//	      Email: maxx@e-taller.net
//         WWW: http://www.e-taller.net/3dlib/
// Description: A PHP class for 3D drawing.
//					 Based on PHP Image API.
//****************************************************************************
// This lib is FREEWARE. 
// You can use it anywhere. You can do anything with it.
// I am not responsible for anything.
// If you dont mind, just send me an URL of the project
// where you used this file (just for my curiousity).
//****************************************************************************
// Oh, yeah... if you are going to improve this lib in some way,
// I'd appreciate it very much if you sent me your code.
//                                              Thanks.
//****************************************************************************
// P.S. For best viewing of the code Tab size 3 is recommended
//****************************************************************************
// P.P.S. The code IS NOT optimized for speed! It's slow enough :(
//			 All I needed was just some funcionality.
//****************************************************************************
/* CHANGE HISTORY
0.11	(May-2001)	-bug fix. now it works faster
0.1	(Nov 2000)	-first release
*/
//****************************************************************************

class C3DLib {
	var $trans;					// Array of transformations
	var $havemat=false;			// Is Transformation Matrix defined?
	var $tmat;					// Transformation Matrix
	var $chart_font=2;		// Font of text in Chart3d()
	var $chart_prec=0;		// Precision of float numbers in Chart3d()
	var $chart_white=20;		// %% of white space between bars in Chart3d()
	var $chart_lingrid=2;	// Number of font lines between to auto-grid lines in Chart3d()
	var $fltAntialias;		// Antialiasing filter to use in Filter()

	//*************************************************************************
	// Constructor: C3dLib()
	// $dx and $dy are coordinates of (X,Y,Z)=(0;0;0) on the screen
	// $dflt : whether to set up default projection (recommended)
	function C3dLib($dx=0, $dy=0, $dflt=1) {
		// Initializing Antialiasing filter
		$this->fltAntialias = array(
			array(1,2,1),
			array(2,4,2),
			array(1,2,1)
		);
		// Adding some transformations to get a more or less 
		// good looking picture
		if($dflt) {
			$this->mAddTransform('Turn', 'X', rad2deg(M_PI /2 * 0.85));
			$this->mAddTransform('Turn', 'Y', -22.5);
			$this->mAddTransform('Turn', 'Z', 5.25);
		}
		if($dx) $this->mAddTransform('Move', 'X', $dx);
		if($dy) $this->mAddTransform('Move', 'Y', $dy);
	}
	//*************************************************************************
	// Method: mClearTransform()
	// Removes all transformations. screen = plane(x,y,0)
	function mClearTransform() {
		unset($this->tmat); 
		for($i=0;$i<4;$i++) $this->tmat[$i][$i] = 1;
		$this->havemat = false;
	}
	//*************************************************************************
	// Method: mAddTransform()
	// Adds new transformation.
	// $typ : transformation type = [Move" | "Turn" | "Scale" | "Mirror]
	// $sub : transformation subject = [X" | "Y" | "Z]
	//			 for "Mirror" type additional subjects are [XOY" | "XOZ" | "YOZ" | "O]
	//			 for "Scale" type additional subjects are [XYZ]
	// $val : parameter. is not used for "Mirror"
	//			 for "Move" it is an offset
	//			 for "Turn" it is an angle in degrees
	//			 for "Scale" it is a scaling coefficient
	function mAddTransform($typ, $sub="", $val=0) {
		$c = sizeof($this->trans);
		$this->trans[$c]->typ = $typ;
		$this->trans[$c]->sub = $sub;
		$this->trans[$c]->val = $val;
		$this->havemat = false;
		return sizeof($this->trans);
	}
	//*************************************************************************
	// Method: mApplyTransform()
	// Converts coordinates in $point which is an array(X,Y,Z) 
	// according to Transformation Matrix
	function mApplyTransform($point, $isfull=0) {
		$point[3] = 1;
		if(!$this->havemat) $this->mCalculateMatrix();
		if($isfull) $limit = 4; else $limit = 2;
		for($i=0;$i<$limit;$i++) for($j=0;$j<4;$j++)
			$x[$i] += $point[$j] * $this->tmat[$j][$i];
		return $x;
	}
	//*************************************************************************
	// Method: mMatrixMultiply()
	// Multiplies two matrixes
	function mMatrixMultiply($a, $b) {
		for($i=0;$i<4;$i++) for($j=0;$j<4;$j++)
			for($k=0;$k<4;$k++)
				$res[$i][$j] += $a[$i][$k]*$b[$k][$j];
		return $res;
	}
	//*************************************************************************
	// Method: mCalculateMatrix()
	// Recalculates Transformation Matrix
	function mCalculateMatrix() {
		$this->mClearTransform();
		if(!sizeof($this->trans)) return $this->tmat;
		reset($this->trans); while(list($k,$v) = each($this->trans)) {
			$fname = 'mMat' . $v->typ . $v->sub;
			$this->tmat = $this->mMatrixMultiply($this->tmat, $this->$fname($v->val));
		}
		$this->havemat = true;
		return $this->tmat;
	}

	//*************************************************************************
	// The following are matrixes for various transformations
	//*************************************************************************
	function mMatTurnX($alpha) { 
		$a = Sin(deg2rad($alpha)); $b = Cos(deg2rad($alpha));
		return array(
			array(1, 0, 0, 0),
			array(0, $b, $a, 0),
			array(0, -$a, $b, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatTurnY($beta) {
		$a = Sin(deg2rad($beta)); $b = Cos(deg2rad($beta));
		return array(
			array($b, 0, -$a, 0),
			array(0, 1, 0, 0),
			array($a, 0, $b, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatTurnZ($gama) {
		$a = Sin(deg2rad($gama)); $b = Cos(deg2rad($gama));
		return array(
			array($b, $a, 0, 0),
			array(-$a, $b, 0, 0),
			array(0, 0, 1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMoveX($dx) {
		return array(
			array(1, 0, 0, 0),
			array(0, 1, 0, 0),
			array(0, 0, 1, 0),
			array($dx, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMoveY($dy) {
		return array(
			array(1, 0, 0, 0),
			array(0, 1, 0, 0),
			array(0, 0, 1, 0),
			array(0, $dy, 0, 1));
	}
	//*************************************************************************
	function mMatMoveZ($dz) {
		return array(
			array(1, 0, 0, 0),
			array(0, 1, 0, 0),
			array(0, 0, 1, 0),
			array(0, 0, $dz, 1));
	}
	//*************************************************************************
	function mMatScaleX($sx) {
		return array(
			array($sx, 0, 0, 0),
			array(0, 1, 0, 0),
			array(0, 0, 1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatScaleY($sy) {
		return array(
			array(1, 0, 0, 0),
			array(0, $sy, 0, 0),
			array(0, 0, 1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatScaleZ($sz) {
		return array(
			array(1, 0, 0, 0),
			array(0, 1, 0, 0),
			array(0, 0, 0, $sz),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatScaleXYZ($s) {
		return array(
			array($s, 0, 0, 0),
			array(0, $s, 0, 0),
			array(0, 0, $s, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMirrorX($val=0) {
		return array(
			array(-1, 0, 0, 0),
			array(0, 1, 0, 0),
			array(0, 0, 1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMirrorY($val=0) {
		return array(
			array(1, 0, 0, 0),
			array(0, -1, 0, 0),
			array(0, 0, 1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMirrorZ($val=0) {
		return array(
			array(1, 0, 0, 0),
			array(0, 1, 0, 0),
			array(0, 0, -1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMirrorXOY($val=0) {
		return array(
			array(-1, 0, 0, 0),
			array(0, -1, 0, 0),
			array(0, 0, 1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMirrorXOZ($val=0) {
		return array(
			array(-1, 0, 0, 0),
			array(0, 1, 0, 0),
			array(0, 0, -1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMirrorYOZ($val=0) {
		return array(
			array(1, 0, 0, 0),
			array(0, -1, 0, 0),
			array(0, 0, -1, 0),
			array(0, 0, 0, 1));
	}
	//*************************************************************************
	function mMatMirrorO($val=0) {
		return array(
			array(-1, 0, 0, 0),
			array(0, -1, 0, 0),
			array(0, 0, -1, 0),
			array(0, 0, 0, 1));
	}

	//*************************************************************************
	// Some functions from PHP Image API adapted for using them in 3D space
	//*************************************************************************

	//*************************************************************************
	function mFill($image, $x, $col) {
		$xt = $this->mApplyTransform($x);
		return ImageFill($image, $xt[0], $xt[1], $col);
	}
	//*************************************************************************
	// $pts is just an array of 3D-points
	// $pts = array( array(x1,y1,z1), ... , array(xN,yN,zN))
	function mFilledPolygon($image, $pts, $col) {
		if(!sizeof($pts)) return 0; else $sz = sizeof($pts);
		$cz = 0;
		for($i=0; $i<$sz; $i++) {
			$xt = $this->mApplyTransform($pts[$i]);
			$ptst[$cz++] = $xt[0];
			$ptst[$cz++] = $xt[1];
		}
		return ImageFilledPolygon($image, $ptst, $cz/2, $col);
	}
	//*************************************************************************
	function mPolygon($image, $pts, $col) {
		if(!sizeof($pts)) return 0; else $sz = sizeof($pts);
		$cz = 0;
		for($i=0; $i<$sz; $i++) {
			$xt = $this->mApplyTransform($pts[$i]);
			$ptst[$cz++] = $xt[0];
			$ptst[$cz++] = $xt[1];
		}
		return ImagePolygon($image, $ptst, $cz/2, $col);
	}
	//*************************************************************************
	function mFillToBorder($image, $x, $border, $col) {
		$xt = $this->mApplyTransform($x);
		return ImageFillToBorder($image, $xt[0], $xt[1], $border, $col);
	}
	//*************************************************************************
	function mLine($image, $x1, $x2, $col) {
		$xt1 = $this->mApplyTransform($x1);
		$xt2 = $this->mApplyTransform($x2);
		return ImageLine($image, $xt1[0], $xt1[1], $xt2[0], $xt2[1], $col);
	}
	//*************************************************************************
	function mDashedLine($image, $x1, $x2, $col) {
		$xt1 = $this->mApplyTransform($x1);
		$xt2 = $this->mApplyTransform($x2);
		return ImageDashedLine($image, $xt1[0], $xt1[1], $xt2[0], $xt2[1], $col);
	}
	//*************************************************************************
	function mSetPixel($image, $x, $col) {
		$xt = $this->mApplyTransform($x);
		return ImageSetPixel($image, $xt[0], $xt[1], $col);
	}

	//*************************************************************************
	// Some new functions
	//*************************************************************************

	//*************************************************************************
	// Method: mBar3d()
	//	Draws 3 "visible" sides of a 3D Bar. 
	// Why quoted? Try to change a projection :)
	// Works fine for _default_ projection. Do you need more? Do that yourself
	// $x=array(X,Y,Z) : coordinates of bar
	// $dx, $dy, $dz : bar's dimensions
	// $col : color
	// $brd : border color (without border by default)
	function mBar3d($image, $x, $dx, $dy, $dz, $col, $brd=-1) {
		$P = array(	array($x[0],$x[1],$x[2]+$dz), array($x[0],$x[1]-$dy,$x[2]+$dz),
						array($x[0]+$dx,$x[1]-$dy,$x[2]+$dz), array($x[0]+$dx,$x[1],$x[2]+$dz));
		$this->mFilledPolygon($image, $P, $col);
		if($brd > 0) $this->mPolygon($image, $P, $brd);
		$P = array(	array($x[0]+$dx,$x[1],$x[2]+$dz), array($x[0]+$dx,$x[1]-$dy,$x[2]+$dz),
						array($x[0]+$dx,$x[1]-$dy,$x[2]), array($x[0]+$dx,$x[1],$x[2]));
		$this->mFilledPolygon($image, $P, $col);
		if($brd > 0) $this->mPolygon($image, $P, $brd);
		$P = array(	array($x[0],$x[1],$x[2]), array($x[0]+$dx,$x[1],$x[2]),
						array($x[0]+$dx,$x[1],$x[2]+$dz), array($x[0],$x[1],$x[2]+$dz));
		$this->mFilledPolygon($image, $P, $col);
		if($brd > 0) $this->mPolygon($image, $P, $brd);
	}

	//*************************************************************************
	// Draws 3D chart
	// $image	: where to draw
	// $DAT		: array(data1, data2, ... , dataN)
	// $LEG		: array(legend1, legend2, .. , legendN)
	// $xmax, $ymax, $zmax : maximum dimensions of the chart
	// $col		: array($color_index1, $color_index2, ... , $color_indexN)
	//				  read the code below and find out other types of color
	// $grid		: grid lines mode => 
	//					  -1 : auto grid; 
	//						0 : no grid; 
	//					  >0 : distance between two grid lines
	// $xtit		: name of X axis
	// $ytit		: name of Y axis
	function mChart3d($image, &$DAT, &$LEG, $xmax, $ymax, $zmax, $col, $grid=-1, $xtit="", $ytit="") {
		if(($bar_num = sizeof($DAT)) < 1) return 0;
		if(($xmax <= 0) || ($ymax <= 0) || ($zmax <= 0)) return 0;
		reset($DAT); while(list($k,$v) = each($DAT)) if($max_dat < $v) $max_dat = $v;

		// Colors
		if(!isset($col[0])) return 0;
		$color_num = 0; while(isset($col[$color_num])) ++$color_num;
		if(($black = ImageColorExact($image,0,0,0)) == -1) $black = ImageColorAllocate($image,0,0,0);
		$axis_col = (isset($col[axis])) ? $col[axis] : $black;
		$grid_col = (isset($col[grid])) ? $col[grid] : $black;
		$xleg_col = (isset($col[xlegend])) ? $col[xlegend] : $axis_col;
		$yleg_col = (isset($col[ylegend])) ? $col[ylegend] : $axis_col;
		$name_col = (isset($col[title])) ? $col[title] : $axis_col;
		$bord_col = (isset($col[border])) ? $col[border] : $axis_col;

		// Grid lines
		if($grid) {
			$this->mLine($image, array(0,0,0), array($xmax,0,0), $axis_col);
			$this->mLine($image, array(0,0,0), array(0,$ymax,0), $axis_col);
			$this->mLine($image, array(0,0,0), array(0,0,$zmax), $axis_col);
			$P = array(array(0,0,0),array($xmax,0,0),array($xmax,$ymax,0),array(0,$ymax,0));
			$this->mPolygon($image, $P, $axis_col);

			if($grid > 0) {
				$grid_num = floor($max_dat/$grid)+1;
				$max_val = $grid_num * $grid;
			} else {										// Auto-grid
				$grid_num = floor($zmax/ImageFontHeight($this->chart_font)/$this->chart_lingrid)+1;
				$max_val = $max_dat;
				$grid = $max_dat / $grid_num;
			}
			if($grid <= 0) $grid = 1;
			if($max_val <= 0) $max_val = 1;
			if($max_dat <= 0) $max_dat = 1;

			for($i=1; $i<=$grid_num; $i++) {
				$z = ($zmax/$max_val)*((double)sprintf("%.".$this->chart_prec."f", $grid*$i));
				$this->mLine($image, array(0,0,$z), array($xmax+2,0,$z), $grid_col);
				$this->mLine($image, array(0,0,$z), array(0,$ymax+2,$z), $grid_col);
				$label = ' '.sprintf('%.'.$this->chart_prec.'f', $grid*$i); 
				if($lab_max < strlen($label)) $lab_max = strlen($label);
				$x = $this->mApplyTransform(array($xmax,0,$z));
				ImageString($image, 
					$this->chart_font, 
					$x[0],	
					$x[1]-(ImageFontHeight($this->chart_font)/2), 
					$label, $yleg_col);
			}
		} else $max_val = $max_dat;

		// Titles
		if(strlen($xtit)) {
			$x = $this->mApplyTransform(array($xmax/2,0,$zmax));
			$y = $this->mApplyTransform(array(0,0,$zmax));
			ImageString($image, $this->chart_font,
				$x[0]-(ImageFontWidth($this->chart_font)*strlen($xtit)/2),
				$y[1]-(ImageFontHeight($this->chart_font)*3),
				$xtit, $name_col);
		}
		if(strlen($ytit)) {
			$x = $this->mApplyTransform(array(0,$ymax,$zmax/2));
			ImageStringUp($image, $this->chart_font,
				$x[0]-(ImageFontHeight($this->chart_font)*3/2),
				$x[1]+(ImageFontWidth($this->chart_font)*strlen($ytit)/2),
				$ytit, $name_col);
		}

		// Bars
		$bar_space = (($xmax/100)*$this->chart_white)/(sizeof($DAT)+1);	
		$bar_wid = ($xmax - $bar_space*(sizeof($DAT)+1))/sizeof($DAT);
		$bar_cnt = 0;
		reset($DAT); while(list($k,$v) = each($DAT)) {
			$this->mDashedLine($image, 
				array($bar_space+($bar_wid+$bar_space)*$bar_cnt+$bar_wid,0,0),
				array($bar_space+($bar_wid+$bar_space)*$bar_cnt+$bar_wid,0,$zmax),
				$grid_col);
			$this->mBar3d($image, 
				array( $bar_space+($bar_wid+$bar_space)*$bar_cnt, $ymax, 0 ),
				$bar_wid, $ymax, $zmax/$max_val*$v, 
				$col[(int)($bar_cnt % $color_num)], $bord_col);

			$x = $this->mApplyTransform(array(($bar_wid+$bar_space)*($bar_cnt+1)-($bar_wid/2), $ymax, 0));
			$lab = sprintf('%.'.$this->chart_prec.'f ', $v);
			ImageStringUp($image, 
				$this->chart_font, 
				$x[0]-ImageFontWidth($this->chart_font), 
				$x[1]+(strlen($lab)*ImageFontWidth($this->chart_font)), 
				$lab, $xleg_col);

			$x = $this->mApplyTransform(array(($bar_wid+$bar_space)*($bar_cnt+1)-($bar_wid/2), 0, $zmax));
			$lab = ' '.$LEG[$k];
			ImageStringUp($image, 
				$this->chart_font, 
				$x[0]-ImageFontWidth($this->chart_font), 
				$x[1], 
				$lab, $xleg_col);
			++$bar_cnt;
		}
		return $image;
	}

	//*************************************************************************
	// Filters an image
	// $image	: where to draw
	// $FLT		: Filter's matrix. $this->fltAntialias by default
	function mFilter($image, $FLT=array()) {
		if(($fsiz=sizeof($FLT)) < 1) { $FLT = &$this->fltAntialias; $fsiz=sizeof($FLT); }
		$frng = ($fsiz-1) >> 1; $xx = ImageSX($image); $yy = ImageSY($image);
		$oi = ImageCreate($xx, $yy);
		ImageCopy($oi, $image, 0,0, 0,0, $xx, $yy);
		ImageDestroy($image);
		$image = ImageCreate($xx, $yy);

		for($j=0; $j<$yy; $j++) for($i=0; $i<$xx; $i++) {
			$clr1=0; $clr2=0; $clr3=0; $wgh=0;
			for($r1=-$frng; $r1<=$frng; $r1++) for($r2=-$frng; $r2<=$frng; $r2++) {
				if((($i+$r1)<0)||(($i+$r1)>=$xx)||(($j+$r2)<0)||(($j+$r2)>=$yy)) continue;
				$rx = $r1+$frng; $ry = $r2+$frng;
				if(!$FLT[$rx][$ry]) continue;
				$wgh += $FLT[$rx][$ry];
				$ca = ImageColorsForIndex($oi, ImageColorAt($oi, $i+$r1, $j+$r2));
				$clr1 += $ca[red]*$FLT[$rx][$ry]; $clr2 += $ca[green]*$FLT[$rx][$ry]; $clr3 += $ca[blue]*$FLT[$rx][$ry];
			}
			if(!$wgh) continue;
			$clr1 /= $wgh; $clr2 /= $wgh; $clr3 /= $wgh;
			if(($oc = ImageColorExact($image, $clr1, $clr2, $clr3)) < 0) $oc = ImageColorAllocate($image, $clr1, $clr2, $clr3);
			ImageSetPixel($image, $i, $j, $oc);
		}
		return $image;
	}
}
?>