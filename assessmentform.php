<?php
//include_once("includes/topwrapper.php");
?>
    <script>
        var rowCount = 1;
        $('#loading_indicator').hide();
        function validateForm() {
            var recep_vocab = $("#receptive_vocab").val();
            var letter_id = $("#letter_id").val();
            var dec_sight = $("#dec_sight_words").val();
            var rhyming = $("#rhyming").val();
            var blending = $("#blending").val();
            var nonword_rep = $("nonword_rep").val();

            console.log( "Called validateForm" );

            // receptive vocab max 18
            if ( recep_vocab < 0 || recep_vocab > 18) {
                console.log( "recep vocab score bad" );
            }
            // letter id ma 80
            if ( letter_id < 0 || letter_id > 80) {
                console.log( "letter score bad" );
            }
            // decodable and sight words max 11
            if ( dec_sight < 0 || dec_sight > 11) {
                console.log( "dec sight score bad" );
            }
            // rhyming max 6
            if ( rhyming < 0 || rhyming > 6) {
                console.log( "rhyming score bad" );
            }
            // blending max 6
            if ( blending < 0 || blending > 6) {
                console.log( "belding score bad" );
            }
            // non word rep max 6
            if ( nonword_rep < 0 || nonword_rep > 6) {
                console.log( "nonword_repition score bad" );
            }
        }

        function submitAnimation() {
            // indicate they can't change info
            $('#loading_indicator').show();
            $('#table_div').fadeTo(500,0.2);
            $('button').each(function(){
                this.disabled = true;
                console.log("disabled" + this.disabled)
            });

            var data = {};
            data["table_rows"] = rowCount;
            $('#assessment_table input').each (function() {
                data[this.name] = this.value;
            });

            $('#assessment_table select').each (function() {
                data[this.name] = this.value;
            });

            $('#assessment_table textarea').each (function() {
                data[this.name] = this.value;
            });

            $.ajax({
                method: "POST",
                url: "includes/ingestassessmentdata.php",
                data: data
            }).done(function( msg ){
                $('.box-footer').append(msg);
                window.setTimeout(function() { $(".alert-success").alert('close');}, 4000);
                window.setTimeout(function() {
                    $('#loading_indicator').hide();
                    $('#table_div').fadeTo(500,1.0);
                    $('button').each(function(){
                        this.disabled = false;
                    });
                    // clear the table of added rows
                    $('tbody').children('tr').not(':first').remove();
                    // clear first row of input data
                    $('input').val('');
                    rowCount = 1;
                }, 1000);
            });
        }

        function addRow() {
            var row = '<tr><td class=" "><input class="form-control" type="text"   id="student_id"   name="student_id" ></td> \
                       <td class=" "><input class="form-control" type="text"   id="assessment_phase" name="assessment_phase" ></td> \
                       <td class=" "><input class="form-control" type="number" id="age"              name="age" ></td> \
                       <td class=" "><select class="form-control" type="text"   id="gender"           name="gender" ><option disabled selected> -- select -- </option><option value="M">M</option><option value="F">F</option></select></td> \
                       <td class=" "><input class="form-control" type="date"   id="testing_date"     name="testing_date" ></td> \
                       <td class=" "><input class="form-control" type="number" id="receptive_vocab"  name="receptive_vocab" ></td> \
                       <td class=" "><input class="form-control" type="number" id="letter_id_alpha"  name="letter_id_alpha" ></td> \
                       <td class=" "><input class="form-control" type="number" id="letter_id_rand"   name="letter_id_rand" ></td> \
                       <td class=" "><input class="form-control" type="number" id="sound_letter_id"  name="sound_letter_id" ></td> \
                       <td class=" "><input class="form-control" type="number" id="dec_sight_words"  name="dec_sight_words" ></td> \
                       <td class=" "><input class="form-control" type="number" id="rhyming"          name="rhyming" ></td> \
                       <td class=" "><input class="form-control" type="number" id="blending"         name="blending" ></td> \
                       <td class=" "><input class="form-control" type="number" id="nonword_rep"      name="nonword_rep" required=""></td> \
                       <td class=" "><textarea rows="1" cols="50" class="form-control" type="text"   id="comments"         name="comments"></textarea></td></tr>';
            // taken from stack overflow
            $('#assessment_table').find('tbody:last').append(row);
            $('tr:last').find('.form-control').each (function() {
                // add a suffix of the row to the name and id of each new input
                this.id = this.id + rowCount;
                this.name = this.name + rowCount;
            });
            rowCount = rowCount + 1;
        }
    </script>

    <section class="content">
        <div class="row">
            <section class="col-lg-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="box-title">
                            Assessment Entry Form
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="container-fluid">
                            <div class="table-responsive" id="table_div">
                                <img src="img/ajax-loader.gif" id="loading_indicator" name="loading_indicator" hidden="true">
                                <table class="table table-bordered table-striped dataTable" id="assessment_table" name="assessment_table">
                                    <thead>
                                    <tr>
                                        <th><label for="student_id0">Student ID</label></th>
                                        <th><label for="assessment_phase0">Assesment Phase</label></th>
                                        <th><label for="age0">Age</label></th>
                                        <th width="80px"><label for="gender0">Gender</label></th>
                                        <th><label for="testing_date0">Date of Testing</label></th>
                                        <th><label for="receptive_vocab0">Receptive Vocab</label></th>
                                        <th><label for="letter_id_alpha0">Letter Name Identification (Alphabetical Order)</label></th>
                                        <th><label for="letter_id_rand0">Letter Name Identification (Random Order)</label></th>
                                        <th><label for="sound_letter_id0">Sound Letter Identification</label></th>
                                        <th><label for="dec_sight_words0">Decodable and Sight Words</label></th>
                                        <th><label for="rhyming0">Rhyming</label></th>
                                        <th><label for="blending0">Blending</label></th>
                                        <th><label for="nonword_rep0">Nonword Repetition</label></th>
                                        <th><label for="comments0">Comments</label></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class=" "><input class="form-control" type="text" id="student_id0"       name="student_id0" ></td>
                                        <td class=" "><input class="form-control" type="text"   id="assessment_phase0" name="assessment_phase0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="age0"              name="age0" ></td>
                                        <td class=" "><select class="form-control" type="text"   id="gender0"          name="gender0" ><option disabled selected> -- select -- </option><option value="M">M</option><option value="F">F</option></select></td>
                                        <td class=" "><input class="form-control" type="date"   id="testing_date0"     name="testing_date0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="receptive_vocab0"  name="receptive_vocab0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="letter_id_alpha0"  name="letter_id_alpha0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="letter_id_rand0"   name="letter_id_rand0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="sound_letter_id0"  name="sound_letter_id0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="dec_sight_words0"  name="dec_sight_words0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="rhyming0"          name="rhyming0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="blending0"         name="blending0" ></td>
                                        <td class=" "><input class="form-control" type="number" id="nonword_rep0"      name="nonword_rep0" ></td>
                                        <td class=" "><textarea rows="1" cols="50" class="form-control" type="text"   id="comments0"         name="comments"></textarea></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer">
                                <button type="button" class="btn btn-info" onclick="addRow();">Add Row</button>
                                <br />
                                <br />
                                <br />
                                <button type="button" class="btn btn-primary" id="submit_button" onclick="submitAnimation();">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>

<?php //include_once("includes/bottomwrapper.php"); ?>