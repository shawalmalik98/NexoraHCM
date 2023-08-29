"use strict";

// Class definition
var ActiveMaleFemaleChart = function () {
    // Private methods

    var initChart = function(active_employees,active_male, active_female) {
        var el = document.getElementById('active_male_female'); 
        if (!el) {
            return;
        }

        var options = {
            size: el.getAttribute('data-kt-size') ? parseInt(el.getAttribute('data-kt-size')) : 70,
            lineWidth: el.getAttribute('data-kt-line') ? parseInt(el.getAttribute('data-kt-line')) : 11,
            rotate: el.getAttribute('data-kt-rotate') ? parseInt(el.getAttribute('data-kt-rotate')) : 145,            
            //percent:  el.getAttribute('data-kt-percent') ,
        }

        var canvas = document.createElement('canvas');
        var span = document.createElement('span'); 
            
        if (typeof(G_vmlCanvasManager) !== 'undefined') {
            G_vmlCanvasManager.initElement(canvas);
        }

        var ctx = canvas.getContext('2d');
        canvas.width = canvas.height = options.size;

        el.appendChild(span);
        el.appendChild(canvas);

        ctx.translate(options.size / 2, options.size / 2); // change center
        ctx.rotate((-1 / 2 + options.rotate / 180) * Math.PI); // rotate -90 deg

        //imd = ctx.getImageData(0, 0, 240, 240);
        var radius = (options.size - options.lineWidth) / 2;

        var drawCircle = function(color, lineWidth, percent) {
            percent = Math.min(Math.max(0, percent || 1), 1);
            ctx.beginPath();
            ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, false);
            ctx.strokeStyle = color;
            ctx.lineCap = 'round'; // butt, round or square
            ctx.lineWidth = lineWidth
            ctx.stroke();
        };

        if(active_male == 0){
            active_male = 0.0001;
        }
        if(active_female == 0){
            active_female = 0.0001;
        }
        // Init 
        drawCircle('#E4E6EF', options.lineWidth, 100 / 100); 
            if(active_employees > 0){
              
                drawCircle(KTUtil.getCssVariableValue('--kt-success'), options.lineWidth, (active_male+active_female)/active_employees);   
                drawCircle(KTUtil.getCssVariableValue('--kt-primary'), options.lineWidth, (active_male)/active_employees);
              
            }
    }
     
    return {
        init: function (active_employees,active_male, active_female) {
            initChart(active_employees,active_male, active_female);
        } 
    }
}();


// Class definition
var DeactiveMaleFemaleChart = function () {
    // Private methods

    var initChart = function(deactive_employees,deactive_male, deactive_female) {
        var el = document.getElementById('deactive_male_female'); 

        if (!el) {
            return;
        }

        var options = {
            size: el.getAttribute('data-kt-size') ? parseInt(el.getAttribute('data-kt-size')) : 70,
            lineWidth: el.getAttribute('data-kt-line') ? parseInt(el.getAttribute('data-kt-line')) : 11,
            rotate: el.getAttribute('data-kt-rotate') ? parseInt(el.getAttribute('data-kt-rotate')) : 145,            
            //percent:  el.getAttribute('data-kt-percent') ,
        }

        var canvas = document.createElement('canvas');
        var span = document.createElement('span'); 
            
        if (typeof(G_vmlCanvasManager) !== 'undefined') {
            G_vmlCanvasManager.initElement(canvas);
        }

        var ctx = canvas.getContext('2d');
        canvas.width = canvas.height = options.size;

        el.appendChild(span);
        el.appendChild(canvas);

        ctx.translate(options.size / 2, options.size / 2); // change center
        ctx.rotate((-1 / 2 + options.rotate / 180) * Math.PI); // rotate -90 deg

        //imd = ctx.getImageData(0, 0, 240, 240);
        var radius = (options.size - options.lineWidth) / 2;

        var drawCircle = function(color, lineWidth, percent) {
            percent = Math.min(Math.max(0, percent || 1), 1);
            ctx.beginPath();
            ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, false);
            ctx.strokeStyle = color;
            ctx.lineCap = 'round'; // butt, round or square
            ctx.lineWidth = lineWidth
            ctx.stroke();
        };

        if(deactive_male == 0){
            deactive_male = 0.0001;
        }
        if(deactive_female == 0){
            deactive_female = 0.0001;
        }
        
        // Init 
        drawCircle('#E4E6EF', options.lineWidth, 100 / 100); 
        if(deactive_employees > 0){
            drawCircle(KTUtil.getCssVariableValue('--kt-success'), options.lineWidth, (deactive_male + deactive_female)/deactive_employees);  
            drawCircle(KTUtil.getCssVariableValue('--kt-primary'), options.lineWidth, (deactive_male)/deactive_employees); 
           
        }
      
      
       
    }

    // Public methods
   // Public methods
    return {
        init: function (deactive_employees,deactive_male, deactive_female) {
            initChart(deactive_employees,deactive_male, deactive_female);
        }
    }
}();


var PermanentMaleFemaleChart = function () {
    // Private methods

    var initChart = function(permanent_employees,per_male,per_female) {
        var el = document.getElementById('permanent_male_female'); 

        if (!el) {
            return;
        }

        var options = {
            size: el.getAttribute('data-kt-size') ? parseInt(el.getAttribute('data-kt-size')) : 70,
            lineWidth: el.getAttribute('data-kt-line') ? parseInt(el.getAttribute('data-kt-line')) : 11,
            rotate: el.getAttribute('data-kt-rotate') ? parseInt(el.getAttribute('data-kt-rotate')) : 145,            
            //percent:  el.getAttribute('data-kt-percent') ,
        }

        var canvas = document.createElement('canvas');
        var span = document.createElement('span'); 
            
        if (typeof(G_vmlCanvasManager) !== 'undefined') {
            G_vmlCanvasManager.initElement(canvas);
        }

        var ctx = canvas.getContext('2d');
        canvas.width = canvas.height = options.size;

        el.appendChild(span);
        el.appendChild(canvas);

        ctx.translate(options.size / 2, options.size / 2); // change center
        ctx.rotate((-1 / 2 + options.rotate / 180) * Math.PI); // rotate -90 deg

        //imd = ctx.getImageData(0, 0, 240, 240);
        var radius = (options.size - options.lineWidth) / 2;

        var drawCircle = function(color, lineWidth, percent) {
            percent = Math.min(Math.max(0, percent || 1), 1);
            ctx.beginPath();
            ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, false);
            ctx.strokeStyle = color;
            ctx.lineCap = 'round'; // butt, round or square
            ctx.lineWidth = lineWidth
            ctx.stroke();
        };

        if(per_male == 0){
            per_male = 0.0001;
        }
        if(per_female == 0){
            per_female = 0.0001;
        }
        // Init 
        drawCircle('#E4E6EF', options.lineWidth, 100 / 100); 
        if(permanent_employees > 0){
         
            drawCircle(KTUtil.getCssVariableValue('--kt-success'), options.lineWidth, (per_male+per_female)/permanent_employees); 
            drawCircle(KTUtil.getCssVariableValue('--kt-primary'), options.lineWidth, per_male/permanent_employees);
             
        }
     
    }

    // Public methods
   // Public methods
    return {
        init: function (permanent_employees,per_male, per_female) {
            initChart(permanent_employees,per_male, per_female);
        }
    }
}();


var ProbationMaleFemaleChart = function () {
    // Private methods

    var initChart = function(probation_employees,pro_male,pro_female) {
        var el = document.getElementById('probation_male_female'); 

        if (!el) {
            return;
        }

        var options = {
            size: el.getAttribute('data-kt-size') ? parseInt(el.getAttribute('data-kt-size')) : 70,
            lineWidth: el.getAttribute('data-kt-line') ? parseInt(el.getAttribute('data-kt-line')) : 11,
            rotate: el.getAttribute('data-kt-rotate') ? parseInt(el.getAttribute('data-kt-rotate')) : 145,            
            //percent:  el.getAttribute('data-kt-percent') ,
        }

        var canvas = document.createElement('canvas');
        var span = document.createElement('span'); 
            
        if (typeof(G_vmlCanvasManager) !== 'undefined') {
            G_vmlCanvasManager.initElement(canvas);
        }

        var ctx = canvas.getContext('2d');
        canvas.width = canvas.height = options.size;

        el.appendChild(span);
        el.appendChild(canvas);

        ctx.translate(options.size / 2, options.size / 2); // change center
        ctx.rotate((-1 / 2 + options.rotate / 180) * Math.PI); // rotate -90 deg

        //imd = ctx.getImageData(0, 0, 240, 240);
        var radius = (options.size - options.lineWidth) / 2;

        var drawCircle = function(color, lineWidth, percent) {
            percent = Math.min(Math.max(0, percent || 1), 1);
            ctx.beginPath();
            ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, false);
            ctx.strokeStyle = color;
            ctx.lineCap = 'round'; // butt, round or square
            ctx.lineWidth = lineWidth
            ctx.stroke();
        };

        if(pro_male == 0){
            pro_male = 0.0001;
        }
        if(pro_female == 0){
            pro_female = 0.0001;
        }

        // Init 
        drawCircle('#E4E6EF', options.lineWidth, 100 / 100); 
        if(probation_employees > 0){
            drawCircle(KTUtil.getCssVariableValue('--kt-success'), options.lineWidth, (pro_male + pro_female)/probation_employees);  
            drawCircle(KTUtil.getCssVariableValue('--kt-primary'), options.lineWidth, (pro_male)/probation_employees); 
          
        }
    }

    // Public methods
   // Public methods
    return {
        init: function (probation_employees,pro_male,pro_female) {
            initChart(probation_employees,pro_male,pro_female);
        }
    }
}();


