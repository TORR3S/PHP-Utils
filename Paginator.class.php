<?php
class Paginator {
    private $narts; // Número de articulos totales retornados de la consulta
    private $artpp; // Número de artículos por página deseados por defecto 30 = 2*3*5
    private $npags; // Número de páginas resultantes de la paginación
    private $jumpn; // Número de páginas a saltar para facilitar la navegación
    private $start; // Punto a partir del cual se deven retornar los resultados: '... LIMIT $start, $artpp'
    private $ulcl;  // Valor del atributo class de html para la lista: <ul class="ulcl">
    private $ulid;  // Valor del atributo id de html para la lista: <ul id="identificador">
    private $param; // Nombre del parámetro GET que almacena el número de página actual
    private $addParams; // Parámetros GET adicionales
    private $pag;   // Número de página actual
    private $firstbtn; //Comienzo de la iteración para los botones númericos
    private $lastbtn;  //Tope de la iteración para los botones numéricos
    
    /*
    * Constructor
    */
    function Paginator($narts, $artpp=30, $param='pag', $addParams='', $ulcl='pags', $ulid='', $nbtns=2, $jumpn=10) {
        $this->narts = $narts;
        $this->artpp = $artpp;
        $this->npags = ceil($narts/$artpp); // redondeo por exceso
        $this->jumpn = $jumpn;
        $this->ulid    = $ulid;
        $this->ulcl = $ulcl;
        $this->param = $param;
        $this->addParams = $addParams;
        $this->pag   = 1; // página a mostrar por defecto
        if(isset($_GET[$param])) { // buscar página seleccionada
            $this->pag = intval($_GET[$param]);
            if     ($this->pag < 1)
                    $this->pag = 1;
            else if($this->pag > $this->npags)
                    $this->pag = $this->npags;
        }
        $this->start=($this->pag-1)*$this->artpp;// Punto a partir del cual se deven retornar los resultados: '... LIMIT $start, $artpp'
        $this->adjustButtons($nbtns);
    }
    
    /*
    * Ajusta los botones de navegación a páginas contiguas.
    * Reduce el número de botones al número de páginas si es necesario.
    * Disminuye la cantidad de botones anteriores o posteriores no coherentes añadiendolos al lado opuesto si es necesario.
    */
    function adjustButtons($nbtns) {
        $mt = $nbtns; //número de enlaces superiores respecto a la página actual
        $mb = $nbtns; //número de enlaces inferiores respecto a la página actual
        while($mb+1+$mt > $this->npags) { //disminuir márgenes para ajustar al número de páginas
            $mb--;
            if($mb+1+$mt > $this->npags)
				$mt--;
        }
        $this->firstbtn = $this->pag-$mb; //comienzo de la iteración para los botones númericos
        $this->lastbtn  = $this->pag+$mt; //tope de la iteración para los botones numéricos
        if($this->firstbtn < 1) {          //ajustar rango cuando márgen inferior sea menor que 1
            $this->firstbtn = 1;
            $this->lastbtn  = 1+($mb+$mt);
        }
        if($this->lastbtn>$this->npags) {  //ajustar rango cuando el márgen superior sea mayor que el número total de páginas
            $this->lastbtn=$this->npags;
            $this->firstbtn=$this->npags-($mb+$mt);
        }
    }
    
    /*
    * Retorna el límite inferior a pasar a la clausula LIMIT de SQL.
    */
    function getLimLo() {
        return $this->start;
    }
    
    /*
    * Añade los botones a la botonera de navegación por páginas.
    */
    function addButton($text, $className='', $href='', $title='') {
        $btn = '';
        $btn.= '<li>';
        if($href!='') $btn.= '<a href="?'.$this->addParams.$this->param.'='.$href.'"'.($title!=''?' title="'.$title.'"':'').'>';
        $btn.= '<span'.($className!=''?' class="'.$className.'"':'').'>'.$text.'</span>';
        if($href!='') $btn.= '</a>';
        $btn.= '</li>';
        return $btn;
    }
    
    /* Paginación de los resultados de la base de datos
     * 
     * retorna código html con el siguiente formato:
     * <ul class="pags">
     *        <li><span>&lt;&lt;</span></li>                      //primera página
     *        <li><span>-10</span></li>                           //Salta $jump páginas hacia atras. Se imprime si el número total de páginas es mayor que $jump y se activa solo si el salto hacia atras es coherente
     *        <li><span>&lt;</span></li>                          //anterior página
     *        <li><span>1</span></li>                             //página actual
     *        <li><span><a href="?pag=2">2</a></span></li>         //2ª página
     *        <li><span><a href="?pag=N">N</a></span></li>         //Nª página
     *        <li><span><a href="?pag=2">&gt;</a></span></li>      //siguiente página
     *        <li><span><a href="?pag=11">+10</a></span></li>      //Salta $jump páginas hacia delante. Se imprime si el número total de páginas es mayor que $jump y se activa solo si el salto hacia delante es coherente
     *        <li><span><a href="?pag=Z">&gt;&gt;</a></span></li>  //última página
     * </ul>
     */
    function paginate() {
        $html='';
        if($this->npags>1) {
            $html.= '<ul class="'.$this->ulcl.'"'.($this->ulid!=''? ' id="'.$this->ulid.'"':'').'>';
            // imprimir botones: 'primera', 'saltar $jumpn páginas hacia atrás' y 'anterior'
            if($this->pag==1) { // Estamos en la primera página, los siguientes botones se imprimen inactivos
                                                $html.= $this->addButton('&lt;&lt;');
                if($this->npags > $this->jumpn) $html.= $this->addButton('-'.$this->jumpn); //si existe un total de páginas mayor que $jump
                                                $html.= $this->addButton('&lt;');
            } else {             // No estamos en la primera página
                $html.= $this->addButton('&lt;&lt;', '', '1', 'First');
                if($this->npags > $this->jumpn) { //si existe un total de páginas mayor que $jumpn
                    if($this->pag > $this->jumpn) { //si la página actual es mayor que jumpn: botón salto activo
                        $html.= $this->addButton('-'.$this->jumpn, '', $this->pag-$this->jumpn, '-'.$this->jumpn);
                    } else { // botón salto inactivo
                        $html.= $this->addButton('-'.$this->jumpn);
                    }
                }
                $html.= $this->addButton('&lt;', '', $this->pag-1, 'Prev');
            }
            
            // imprimir los botones numerados
            $i = $this->firstbtn;
            while($i<=$this->lastbtn) {
                if($i==$this->pag) $html.= $this->addButton($i,'active');
                else               $html.= $this->addButton($i,'', $i);
                $i++;
            }
            
            // imprimir botones: 'siguiente', 'saltar $jumpn páginas hacia adelante' y 'última'
            if($this->pag == $this->npags) { // Estamos en la última página, los siguientes botones se imprimen inactivos
                                                $html.= $this->addButton('&gt;');
                if($this->npags > $this->jumpn) $html.= $this->addButton('+'.$this->jumpn);
                                                $html.= $this->addButton('&gt;&gt;');
            } else {                          // No estamos en la última página
                $html.= $this->addButton('&gt;', '', $this->pag+1, 'Next');
                if($this->npags > $this->jumpn) {
                    if($this->pag+$this->jumpn <= $this->npags) {
                        $html.= $this->addButton('+'.$this->jumpn, '', $this->pag+$this->jumpn, '+'.$this->jumpn);
                    } else {
                        $html.= $this->addButton('+'.$this->jumpn);
                    }    
                }
                $html.= $this->addButton('&gt;&gt;', '', $this->npags, 'Last');
            }
            $html.= '</ul>';
        }
        return $html;
    }
}
?>
