@extends('layout.main')


@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Organogram
                   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
              
         
            </div>
         
        </div>
        
    </div>
   
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">

            <div class="card mb-5 mb-xl-8">
   

                <div id="chartDiv1" class="chartDiv" style="height: 100vh"></div>


            </div>

            

            

       
        
        </div>
    
    </div>
</div>

@endsection

@push('js-link')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://code.jscharting.com/latest/jscharting.js"></script>
    <script type="text/javascript" src="https://code.jscharting.com/latest/modules/types.js"></script>

<script>

 const all_employees = {!! json_encode($filtered_employees) !!};
        
    const mySelect = `[${all_employees.reduce((tmp, x, counter) => `${tmp}{ "x": "<b>${x.employee_name}</b> <br><br>${x.department_name} - ${x.designation_name}", "id": "${x.employee_id}",${parent_id(x)}"attributes_role": "Employee"},`, '')}]`;

    function parent_id(x){
        if(x.employee_id != x.parent_id){
            return `"parent":"${x.parent_id}",`;
        }else{
            return ``;
        }
    }

    const employee_hierarchy = mySelect.substring(0, mySelect.length - 2) + mySelect.substring(mySelect.length - 1);

    var palette = JSC.getPalette(0); 
    var highlightColor = '#FF5722'; 
    var chart = JSC.chart( 
      'chartDiv1', 
      { 
        debug: false, 
        type: 'organizational', 
        defaultTooltip_enabled: true, 
        defaultAnnotation: { 
          padding: [5, 10], 
          margin: [5, 10] 
        },  
        defaultSeries_color: '#FAFAFA', 
        defaultPoint: { 
          outline_width: 0, 
          connectorLine: { color: 'gray', width: 2 }, 
          focusGlow: true, 
          states: { 
            mute: { opacity: 1, outline_width: 1 }, 
            select: { 
              enabled: true, 
              outline_width: 2 
            }, 
            hover_outline_width: 2 
          }, 
          events: { 
            click: pointClick, 
            mouseOver: pointMouseOver, 
            mouseOut: pointMouseOut 
          } 
        }, 
        series: [ 
          { 
            points: 
                JSON.parse(employee_hierarchy)
          } 
        ] 
      }, 
      function(c) { 
        c.series() 
          .points() 
          .each(function(p) { 
            p.options( 
              { outline_color: palette[p.y] }, 
              false
            ); 
          }); 
        c.redraw(); 
      } 
    ); 
      
    var selectedLevel = null; 
      
    function pointClick() { 
      var point = this, 
        chart = point.chart; 
      
      reset(chart); 
      
      if (selectedLevel === point.y) { 
        selectedLevel = null; 
        return; 
      } 
      
      selectedLevel = point.y; 
      
      chart 
        .series() 
        .points(function(p) { 
          return p.y === point.y; 
        }) 
        .options({ selected: true }); 
    } 
      
    function pointMouseOver() { 
      var point = this, 
        chart = point.chart; 
      
      
      chart 
        .series() 
        .points(function(p) { 
          return p.y === point.y; 
        }) 
        .options({ muted: true }); 
    } 
      
    function pointMouseOut() { 
      var point = this, 
        chart = this.chart; 
      
      chart 
        .series() 
        .points() 
        .options({ muted: false }); 
      return false; 
    } 
      
    function reset(c) { 
      //reset selected and muted styling 
      c.series() 
        .points() 
        .options({ selected: false, muted: false }); 
    } 
           
</script>
@endpush       