<?php

/*
 * This file is part of FacturaScripts
 * Copyright (C) 2016      Luismipr               <luismipr@gmail.com>.
 * Copyright (C) 2016-2017 Carlos García Gómez    <neorazorx@gmail.com>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * Lpublished by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * LeGNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace FacturaScripts\model;

require_model('articulo.php');
require_model('linea_albaran_cliente.php');
require_model('linea_albaran_proveedor.php');
require_model('linea_factura_cliente.php');
require_model('linea_factura_proveedor.php');

/**
 * Esta clase sirve para guardar la información de trazabilidad del artículo.
 * Números de serie, de lote y albaranes y facturas relacionadas.
 *
 * @author Luismipr              <luismipr@gmail.com>
 * @author Carlos García Gómez   <neorazorx@gmail.com>
 */
class articulo_traza extends \fs_model
{
   /**
    * Clave primaria
    * @var type 
    */
   public $id;
   
   /**
    * Referencia del artículo
    * @var type varchar 
    */
   public $referencia;
   
   /**
    * Numero de serie
    * Clave primaria.
    * @var type varchar 
    */
   public $numserie;
   
   /**
    * Número o identificador del lote
    * @var type 
    */
   public $lote;
   
   /**
    * Id linea albaran venta
    * @var type serial
    */
   public $idlalbventa;
   
   /**
    * id linea factura venta
    * @var type serial
    */
   public $idlfacventa;
   
   /**
    * Id linea albaran compra
    * @var type serial
    */
   public $idlalbcompra;
   
   /**
    * Id linea factura compra
    * @var type serial
    */
   public $idlfaccompra;
   
   public $fecha_entrada;
   public $fecha_salida;
   
   public function __construct($n = FALSE)
   {
      parent::__construct('articulo_trazas');
      if($n)
      {
         $this->id = $this->intval($n['id']);
         $this->referencia = $n['referencia'];
         $this->numserie = $n['numserie'];
         $this->lote = $n['lote'];
         $this->idlalbventa = $this->intval($n['idlalbventa']);
         $this->idlfacventa = $this->intval($n['idlfacventa']);
         $this->idlalbcompra = $this->intval($n['idlalbcompra']);
         $this->idlfaccompra = $this->intval($n['idlfaccompra']);
         
         $this->fecha_entrada = NULL;
         if( isset($n['fecha_entrada']) )
         {
            $this->fecha_entrada = date('d-m-Y', strtotime($n['fecha_entrada']));
         }
         
         $this->fecha_salida = NULL;
         if( isset($n['fecha_salida']) AND ($this->idlalbventa OR $this->idlfacventa) )
         {
            $this->fecha_salida = date('d-m-Y', strtotime($n['fecha_salida']));
         }
      }
      else
      {
         $this->id = NULL;
         $this->referencia = NULL;
         $this->numserie = NULL;
         $this->lote = NULL;
         $this->idlalbventa = NULL;
         $this->idlfacventa = NULL;
         $this->idlalbcompra = NULL;
         $this->idlfaccompra = NULL;
         $this->fecha_entrada = NULL;
         $this->fecha_salida = NULL;
      }
   }
   
   protected function install()
   {
      /// forzamos la comprobación de las tablas necesarias
      new \articulo();
      new \linea_albaran_cliente();
      new \linea_albaran_proveedor();
      new \linea_factura_cliente();
      new \linea_factura_proveedor();
      
      return '';
   }
   
   /**
    * Devuelve la url del albarán o la factura de compra.
    * @return string
    */
   public function documento_compra_url()
   {
      if($this->idlalbcompra)
      {
         $lin0 = new \linea_albaran_proveedor();
         $linea = $lin0->get($this->idlalbcompra);
         if($linea)
         {
            return $linea->url();
         }
      }
      else if($this->idlfaccompra)
      {
         $lin0 = new \linea_factura_proveedor();
         $linea = $lin0->get($this->idlfaccompra);
         if($linea)
         {
            return $linea->url();
         }
      }
      else
      {
         return '#';
      }
   }
   
   /**
    * Devuelve la url del albarán o factura de venta.
    * @return string
    */
   public function documento_venta_url()
   {
      if($this->idlalbventa)
      {
         $lin0 = new \linea_albaran_cliente();
         $linea = $lin0->get($this->idlalbventa);
         if($linea)
         {
            return $linea->url();
         }
      }
      else if($this->idlfaccompra)
      {
         $lin0 = new \linea_factura_proveedor();
         $linea = $lin0->get($this->idlfaccompra);
         if($linea)
         {
            return $linea->url();
         }
      }
      else
      {
         return '#';
      }
   }
   
   /**
    * Devuelve una traza a partir de un $id.
    * @param type $id
    * @return boolean|\articulo_traza
    */
   public function get($id)
   {
      $data = $this->db->select("SELECT * FROM ".$this->table_name." WHERE id = ".$this->var2str($id).";");
      if($data)
      {
         return new \articulo_traza($data[0]);
      }
      else
      {
         return FALSE;
      }
   }
   
   /**
    * Devuelve la traza correspondiente al número de serie $numserie.
    * @param type $numserie
    * @return boolean|\articulo_traza
    */
   public function get_by_numserie($numserie)
   {
      $data = $this->db->select("SELECT * FROM ".$this->table_name." WHERE numserie = ".$this->var2str($numserie).";");
      if($data)
      {
         return new \articulo_traza($data[0]);
      }
      else
      {
         return FALSE;
      }
   }
   
   public function exists()
   {
      if( is_null($this->id) )
      {
         return FALSE;
      }
      else
      {
         return $this->db->select("SELECT * FROM ".$this->table_name." WHERE id = ".$this->var2str($this->id).";");
      }
   }
   
   public function save()
   {
      if( $this->exists() )
      {
         $sql = "UPDATE ".$this->table_name." SET referencia = ".$this->var2str($this->referencia)
                 . ", numserie = ".$this->var2str($this->numserie)
                 . ", lote = ".$this->var2str($this->lote)
                 . ", idlalbventa = ".$this->var2str($this->idlalbventa)
                 . ", idlfacventa = ".$this->var2str($this->idlfacventa)
                 . ", idlalbcompra = ".$this->var2str($this->idlalbcompra)
                 . ", idlfaccompra = ".$this->var2str($this->idlfaccompra)
                 . ", fecha_entrada = ".$this->var2str($this->fecha_entrada)
                 . ", fecha_salida = ".$this->var2str($this->fecha_salida)
                 . "  WHERE id = ".$this->var2str($this->id).";";
         
         return $this->db->exec($sql);
      }
      else
      {
         $sql = "INSERT INTO ".$this->table_name." (referencia,numserie,lote,idlalbventa,"
                 . "idlfacventa,idlalbcompra,idlfaccompra,fecha_entrada,fecha_salida) VALUES "
                 . "(".$this->var2str($this->referencia)
                 . ",".$this->var2str($this->numserie)
                 . ",".$this->var2str($this->lote)
                 . ",".$this->var2str($this->idlalbventa)
                 . ",".$this->var2str($this->idlfacventa)
                 . ",".$this->var2str($this->idlalbcompra)
                 . ",".$this->var2str($this->idlfaccompra)
                 . ",".$this->var2str($this->fecha_entrada)
                 . ",".$this->var2str($this->fecha_salida).");";
         
         if( $this->db->exec($sql) )
         {
            $this->id = $this->db->lastval();
            return TRUE;
         }
         else
         {
            return FALSE;
         }
      }
   }
   
   public function delete()
   {
      return $this->db->exec("DELETE FROM ".$this->table_name." WHERE id = ".$this->var2str($this->id).";");
   }
   
   /**
    * Devuelve todas las trazas de un artículo.
    * @param type $ref
    * @param type $sololibre
    * @return \articulo_traza
    */
   public function all_from_ref($ref, $sololibre = FALSE)
   {
      $lista = array();
      
      $sql = "SELECT * FROM ".$this->table_name." WHERE referencia = ".$this->var2str($ref);
      if($sololibre)
      {
         $sql .= " AND idlalbventa IS NULL AND idlfacventa IS NULL";
      }
      $sql .= " ORDER BY id ASC;";
      
      $data = $this->db->select($sql);
      if($data)
      {
         foreach($data as $d)
         {
            $lista[] = new \articulo_traza($d);
         }
      }
      
      return $lista;
   }
   
   /**
    * Devuelve todas las trazas cuya columna $tipo tenga valor $idlinea
    * @param type $tipo
    * @param type $idlinea
    * @return \articulo_traza
    */
   public function all_from_linea($tipo, $idlinea)
   {
      $lista = array();
      
      $sql = "SELECT * FROM ".$this->table_name." WHERE ".$tipo." = ". $this->var2str($idlinea)." ORDER BY id DESC;";
      $data = $this->db->select($sql);
      if($data)
      {
         foreach($data as $d)
         {
            $lista[] = new \articulo_traza($d);
         }
      }
      
      return $lista;
   }
}
