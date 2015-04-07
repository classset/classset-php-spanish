/**
 *  Copyright 2013 Gabriel Nicolás González Ferreira <gabrielinuz@gmail.com> 
 *
 *  Permission is hereby granted, free of charge, to any person obtaining
 *  a copy of this software and associated documentation files (the
 *  "Software"), to deal in the Software without restriction, including
 *  without limitation the rights to use, copy, modify, merge, publish,
 *  distribute, sublicense, and/or sell copies of the Software, and to
 *  permit persons to whom the Software is furnished to do so, subject to
 *  the following conditions:
 *
 *  The above copyright notice and this permission notice shall be
 *  included in all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 *  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 *  LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 *  OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 *  WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/
 
(function()//forma básica de encapsular
{

//crear objeto XMLHttpRequest
function createAjaxObject() 
{ 
  var obj; //variable que recogerá el objeto
  
  if (window.XMLHttpRequest) 
  { //código para mayoría de navegadores
    obj=new XMLHttpRequest();
  }
  else 
  { //para IE 5 y IE 6
    obj=new ActiveXObject(Microsoft.XMLHTTP);
  }
  
  return obj; //devolvemos objeto
}

//función constructora del objeto:			 
window.AjaxObject = function() 
{
  var newAjax = createAjaxObject();
  this.object = newAjax;
  this.getText = getAjaxText;
  this.loadXML = loadAjaxXML;
  this.loadText = loadAjaxText;
}			

//función para el método objeto.pedirTexto(url,id) 		
function getAjaxText(url,id) 
{
  var newAjax = this.object;
  var ajaxId = id;
  newAjax.open("GET",url,true);
  newAjax.onreadystatechange = function() 
  {  
    if (newAjax.readyState == 4 && newAjax.status == 200)
    {
      var ajaxText = newAjax.responseText;
      document.getElementById(ajaxId).innerHTML = ajaxText;
    }
  }
  newAjax.send(); 
}

/*función del método cargaXML: devuelve el DOM del XML	
como parámetro de la función que le pasamos*/
function loadAjaxXML(url,Function)
{
  var newAjax = this.object; 
  var xmlFunction = Function; 
  newAjax.open("GET",url,true);
  newAjax.onreadystatechange = function()
  { 
    if (newAjax.readyState == 4 && newAjax.status == 200)
    {
      var property = newAjax.responseXML; 
      funcionXML(property);
    }
  }	
  newAjax.send();
}	

//función del método cargaTexto: 
//devuelve el texto del archivo en el parámetro.
function loadAjaxText(url,Function) 
{
  var newAjax = this.object; 
  var textFunction = Function; 
  newAjax.open("GET",url,true);
  newAjax.onreadystatechange = function()
  { 
    if (newAjax.readyState == 4 && newAjax.status == 200)
    {
      var newText = newAjax.responseText; 
      textFunction(newText);
    }
  }	
  newAjax.send();
}
AjaxObject.prototype.loadText = loadAjaxText; 	
		 
//Método pedirPost: envia datos por POST y devolver en un id: 
function getByPost(url,id,data) 
{
  var newAjax = this.object; 
  var ajaxId = id; 
  var ajaxData = data;
  newAjax.open("POST",url,true);
  newAjax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  newAjax.setRequestHeader("Content-length", ajaxData.length);
  newAjax.setRequestHeader("Connection", "close");
  newAjax.onreadystatechange = function()
  {  
    if (newAjax.readyState == 4 && newAjax.status == 200)
    { 
      var ajaxText = newAjax.responseText; 
      document.getElementById(ajaxId).innerHTML = ajaxText;
    }
  }
  newAjax.send(ajaxData); 
} 	
AjaxObject.prototype.getPost = getByPost;	 

//Método cargaPost: envia datos por post y recoge el resultado en el parámetro de una función:
function loadFromPost(url,Function,data) 
{
  var newAjax = this.object; 
  var textFunction = Function; 
  var ajaxData = data;
  newAjax.open("POST",url,true);
  newAjax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  newAjax.setRequestHeader("Content-length", ajaxData.length);
  newAjax.setRequestHeader("Connection", "close");
  newAjax.onreadystatechange = function()
  { 
    if (newAjax.readyState == 4 && newAjax.status == 200)
    {
      var newText=newAjax.responseText; 
      textFunction(newText);
    }
  }	
  newAjax.send(ajaxData);
}
AjaxObject.prototype.loadPost = loadFromPost;

  
})()