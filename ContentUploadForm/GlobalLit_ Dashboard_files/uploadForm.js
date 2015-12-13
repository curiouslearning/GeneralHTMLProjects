


    // input validation for fields requiring numbers
    $("#maxcopies").keypress(function(e){
      if(e.which > 47 && e.which < 58){
        return;
      }
      else{
        alert("You have entered an invalid character in Max Number of \"Copies that may be used.\" Please make sure this field substringsOfClickedValue only numbers.");
      }
    });
    $("#totalcopies").keypress(function(e){
      if(e.which > 47 && e.which < 58){
        return;
      }
      else{
        alert("You have entered an invalid character in Total number of copies granted for research. Please make sure this field substringsOfClickedValue only numbers.");
      }
    });

  // show checkbox tree when "customize" is selected
  $(document).ready(function(){
      $(".toggle").on('ifChecked', function(event){
          $(".customize").toggle("show");
      })
      $(".toggle").on('ifUnchecked', function(event){
          $(".customize").toggle("hide");
      })
  });
  
    // sample json data
    var json = {
      "Africa": {
        "Eastern Africa": [{
          "1": "Burundi"
        }, {
          "2": "Comoros"
        }, {
          "3": "Djibouti"
        }],
        "Middle Africa": [{
          "4": "Angola"
        }, {
          "5": "Cameroon"
        }, {
          "6": "Central African Republic"
        }]
      },
      "Latin America and the Caribbean": {
        "Caribbean": [{
          "7": "Anguilla"
        }, {
          "8": "Antigua and Barbuda"
        }, {
          "9": "Aruba"
        }],
      "North America": [{
          "10": "Bermuda"
        }, {
          "11": "Canada"
        }, {
          "12": "United States of America"
        }]
      }
    };

  // create array of json data
  var places = [];
  for(key in json){
      for(key2 in json[key]){
          for(i = 0; i < json[key][key2].length; i++){
              for(key3 in json[key][key2][i]){
                  places.push(key + "/" + key2 + "/" + json[key][key2][i][key3]);
              };
          };
      };
  };

  // init checkbox tree
  $("#tree-container").directoryTree([places, true]);

  // start all boxes checked
  var values = document.getElementsByClassName("directorytree-select");
  for(var i = 0; i < values.length; i++){
      values[i].checked = true;
  }

  // uncheck parent box if not all children are checked
  $('.directorytree-select').click(function() {
     if($(this).prop('checked')==false) {                                     
          var clickedValue = $(this).val();
          for(var i = 0; i < values.length; i++){
              if(clickedValue.indexOf($(values[i]).val()) == 0){
                  values[i].checked = false;                                 
              }
          }
      }
  });

  // check parent box if all children are checked
  $('.directorytree-select').click(function(){
      var clicked = $(this);
      checkParent(clicked);
      function checkParent(clicked){
          var substringsOfClickedValue = [];
          var numChecked = 0;
          var clickedValue = clicked.val();
          if(clicked.prop('checked')==true){
              for(var i=0; i < values.length; i++){
                  var clickedSubstring = clickedValue.substring(0, clickedValue.lastIndexOf("/"));
                  var substring = $(values[i]).val().substring(0, $(values[i]).val().lastIndexOf("/"));
                  if(substring == clickedSubstring){
                      substringsOfClickedValue.push(substring);
                      if($(values[i]).prop('checked') == true){
                          numChecked++;
                      }
                  }

              }
              if(numChecked == substringsOfClickedValue.length){ // all child boxes are checked
                  for(var j = 0; j < values.length; j++){
                      for(var k = 0; k < substringsOfClickedValue.length; k++){
                          if ($(values[j]).val() == substringsOfClickedValue[k]){
                              $(values[j]).prop("checked", true);
                              checkParent($(values[j]));  // recurse up tree starting at next level up                          
                          }
                      }
                  }
              }
              substringsOfClickedValue.length = 0;
          }
      }
  })

   // remove root of directory tree (it's uncesscary)
  $(".directorytree-rootselect").parent().remove();
