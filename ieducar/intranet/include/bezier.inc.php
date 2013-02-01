<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itajaí								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
*																		 *
*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
*	junto  com  este  programa. Se não, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

class Bezier
{
    var $p0;
    var $p1;
    var $p2;
    var $p3;

    var $curve_points = array();
    var $curve_length = 0;

    function Bezier($p0, $p1, $p2, $p3, $steps)
    {
        $this->p0 = $p0;
        $this->p1 = $p1;
        $this->p2 = $p2;
        $this->p3 = $p3;

        $this->calculate_curve_points($steps);
        $this->calculate_curve_length();
    }

    // Evaluates an individual point on the curve using brute force
    function get_curve_point($t)
    {
        $point['x'] = (pow(1-$t, 3) * $this->p0['x']) + (3*$t*pow(1-$t, 2)*$this->p1['x']) + (3*pow($t, 2)*(1-$t)*$this->p2['x']) + (pow($t, 3)*$this->p3['x']);
        $point['y'] = (pow(1-$t, 3) * $this->p0['y']) + (3*$t*pow(1-$t, 2)*$this->p1['y']) + (3*pow($t, 2)*(1-$t)*$this->p2['y']) + (pow($t, 3)*$this->p3['y']);
        return $point;
    }

    function get_curve_points()
    {
        return $this->curve_points;
    }

    function get_curve_length()
    {
        return $this->curve_length;
    }

    // An approximation to the curve length.  As with everything else,
    // if the underlying step size is small enough, this should give
    // a very accurate result
    function calculate_curve_length()
    {
        $this->curve_length = 0;

        $first = true;
        foreach ($this->curve_points as $t=>$point)
        {
            if ($first)
            {
                $last_x = $point['x'];
                $last_y = $point['y'];
                $first = false;
            }

            $segment = sqrt(pow($last_x - $point['x'], 2) + pow($last_y - $point['y'], 2));
            $this->curve_length += $segment;
            $this->curve_segments[$t] = $segment;

            $last_x = $point['x'];
            $last_y = $point['y'];
        }
    }

    // This should work pretty well if the ratio of steps for
    // calculation of the curve to the steps required in the
    // reparameterization is fairly high.
    //
    // Considering the alternatives, this should serve most
    // purposes just fine.
    function get_reparameterized_curve_points($steps)
    {
        // set new step size
        // subtract the teeny-weeny number to try to account for
        // rounding errors that may make the last point disappear
        $step_size = $this->curve_length/$steps - 0.0000000001;

        // t_point is the distance travelled with the t parameter
        // s_point is the distance travelled with the new s parameter
        $t_point = 0;
        $s_point = 0;

        // loop over each new step
        $s = 0;
        $t = 0;
        while ($s++ <= $steps)
        {
            // loop until we find the segment that contains $s
            while (isset($this->curve_segments[$t]))
            {
                $segment = $this->curve_segments[$t];
                // did we pass $s?
                if (($t_point + $segment) >= $s_point)
                {
                    // prevent division by zero
                    if ($segment == 0)
                    {
                        $fraction = 1;
                    }
                    else
                    {
                        $fraction = ($s_point - $t_point)/$segment;
                    }

                    $this_point = $this->curve_points[$t];
                    if (! isset($this->curve_points[$t-1]))
                    {
                        $last_point = $this_point;
                    }
                    else
                    {
                        $last_point = $this->curve_points[$t-1];
                    }
                    // if the step size of the original parameter ($t) is small enough, this
                    // should be pretty accurate
                    $new_x = $last_point['x'] + ($this_point['x'] - $last_point['x']) * $fraction;
                    $new_y = $last_point['y'] + ($this_point['y'] - $last_point['y']) * $fraction;
                    $points[] = array(  'x'=>$new_x,
                                        'y'=>$new_y);

                    break;
                }
                else
                {
                    // Only increment if we didn't find a point on the last iteration.
                    // This is so that if two new points fall inside one of the old ones
                    // it still works.  This is a bad idea, though, cuz it means the ratio is low
                    $t++;
                    $t_point += $segment;
                }
            }
            // we want to start with an $s_point of 0, so we post increment this
            $s_point += $step_size;
        }

        return $points;
    }

    // Uses forward differencing to calculate the curve points using
    // a constant parameter step size
    function calculate_curve_points($steps)
    {
        $this->curve_points = array();

        $dt = 1 / $steps;
    
        $pre1 = 3*$dt;
        $pre2 = 3*$dt*$dt;
        $pre3 = $dt*$dt*$dt;
        $pre4 = 6*$dt*$dt;
        $pre5 = 6*$dt*$dt*$dt;
    
        $coef1['x'] = $this->p0['x'] - (2*$this->p1['x']) + $this->p2['x'];
        $coef1['y'] = $this->p0['y'] - (2*$this->p1['y']) + $this->p2['y'];
        $coef2['x'] = (3 * ($this->p1['x'] - $this->p2['x'])) - $this->p0['x'] + $this->p3['x'];
        $coef2['y'] = (3 * ($this->p1['y'] - $this->p2['y'])) - $this->p0['y'] + $this->p3['y'];

        $f['x'] = $this->p0['x'];
        $f['y'] = $this->p0['y'];

        $df['x'] = ($this->p1['x']-$this->p0['x'])*$pre1 + $coef1['x']*$pre2 + $coef2['x']*$pre3;
        $df['y'] = ($this->p1['y']-$this->p0['y'])*$pre1 + $coef1['y']*$pre2 + $coef2['y']*$pre3;

        $ddf['x'] = $coef1['x']*$pre4 + $coef2['x']*$pre5;
        $ddf['y'] = $coef1['y']*$pre4 + $coef2['y']*$pre5;

        $dddf['x'] = $coef2['x']*$pre5;
        $dddf['y'] = $coef2['y']*$pre5;

        for ($i=0; $i<=$steps; $i++)
        {
            $this->curve_points[$i]['x'] = $f['x'];
            $this->curve_points[$i]['y'] = $f['y'];
    
            $f['x'] += $df['x'];
            $f['y'] += $df['y'];
    
            $df['x'] += $ddf['x'];
            $df['y'] += $ddf['y'];
    
            $ddf['x'] += $dddf['x'];
            $ddf['y'] += $dddf['y'];
        }
    }
}

?>