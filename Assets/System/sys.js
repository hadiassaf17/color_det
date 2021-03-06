

class SYS{
    static login(that){
        let email = $(that).parents("form").find("input[name='email']").val();
        let password = $(that).parents("form").find("input[name='password']").val();
        let fd = new FormData();
        fd.append("key","login/submitlogin");
        fd.append("email",email);
        fd.append("password",password);
        this.xhr(fd, $(that).attr("to") );
    }
    static xhr(_data,OutContainer,_success){
        let _url = SELF_DIR;
        let cfg = {
            url: _url,
            data: _data,
            method:"POST",
            cache: false,
            contentType: false,
            processData: false,
        };
        cfg.xhr = function(){
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with upload progress
                    //console.log(percentComplete);
                    percentComplete = percentComplete * 100;
                    percentComplete = parseInt(percentComplete);
                    if(OutContainer != null){
                        document.getElementById(OutContainer).innerHTML = "Uploading ..." + percentComplete + " % " ;
                    }
                }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                //Do something with download progress
                //console.log(percentComplete);
                percentComplete = percentComplete * 100;
                percentComplete = parseInt(percentComplete);
                if(OutContainer != null){
                    document.getElementById(OutContainer).innerHTML = "Downloading ..." + percentComplete + " % " ;
                }
            }
            }, false);
            return xhr;
        };
        cfg.success = function (resp) {
            try{
                if (_success) _success(resp,OutContainer);
                else $(`#${OutContainer}`).html(resp);
            }
            catch(error){
                console.log(error);
                if(OutContainer != null){
                    $("#"+OutContainer).html(error);
                }
            }
        }
        $.ajax(cfg);
    }
    static XHRForm(that){
        let fd = new FormData($(that).parents('form')[0]);
        let htmls = $(that).parents('form').find("[name*='html']");
        if(htmls.length > 0){
            for(let i =0; i < htmls.length;i++){
                let html = $(htmls[i]);
                let name = html.attr("name");
                if(CKEDITOR.instances[name]){
                    fd.append(name,CKEDITOR.instances[name].getData());
                }
            }
        }
        SYS.xhr_post(null,fd,"text",$(that).attr('to'),function(r,o){
            $(`#${o}`).html(r);
        });
    }
    static XHRFct(fct,to){
        let fd = new FormData();
        fd.append("fct",fct);
        SYS.xhr_post(null,fd,"text",to,function(r,o){
            $(`#${o}`).html(r);
        });
    }
    static LoadXHR(out,fct){
        let fd = new FormData();
        fd.append("key",fct);
        SYS.xhr_post(null,fd,"text",out,function(r,o){
            $(`#${o}`).html(r);
        });
    }
    static xhr_post(_url,_data,_type,OutContainer,_success,_error =null){
        if(_url == null){
            _url = BASE_DIR ;
        }
        this.OutContainer = OutContainer;
        if(OutContainer == null) OutContainer="CT1";
        if(OutContainer == "no") OutContainer=null;
        if(OutContainer != null){
            var t = document.getElementById(OutContainer);
            if(document.getElementById(OutContainer) == null){
                t = document.createElement('div');
                t.id = OutContainer;
                document.body.appendChild(t);
            }
            document.getElementById(OutContainer).innerHTML = "Loading ...";
            document.getElementById(OutContainer).style.display = "block";
        }
        //console.log(`to url ${_url}`);
        //console.log(`method ${_type}`);
        let cfg = {
            url: _url,
            type: _type,
            data: _data,
            method:"POST",
            cache: false,
            contentType: false,
            processData: false,
        };
        //to track the progress of upload
        cfg.xhr = function(){
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with upload progress
                    //console.log(percentComplete);
                    percentComplete = percentComplete * 100;
                    percentComplete = parseInt(percentComplete);
                    if(OutContainer != null){
                        document.getElementById(OutContainer).innerHTML = "Uploading ..." + percentComplete + " % " ;
                    }
                }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                //Do something with download progress
                //console.log(percentComplete);
                percentComplete = percentComplete * 100;
                percentComplete = parseInt(percentComplete);
                if(OutContainer != null){
                    document.getElementById(OutContainer).innerHTML = "Downloading ..." + percentComplete + " % " ;
                }
            }
            }, false);
            return xhr;
        };

        

        // handle error from xhr
        cfg.error= function (jqXHR, textStatus) {
            console.log({ "Error- Status: ": textStatus," jqXHR Status: ": jqXHR.status, " jqXHR Response Text:": jqXHR.responseText });
            $("#"+OutContainer).html(jqXHR.responseText);
            
            if(_error){
                _error(jqXHR, textStatus);
            }
            //redo request if server responded with error
            if(jqXHR.status == 500 ){
                //SYS.xhr_post(_url,_data,_type,OutContainer,_success,_error);
            }
            else if(jqXHR.status == 419){
                alert("Session ended, need to refresh page");
                location = location;
            }
        };
        //success of ajax call
        cfg.success = function (resp) {
            try{

                _success(resp,OutContainer);
            }
            catch(error){
                console.log(error);
                if(OutContainer != null){
                    $("#"+OutContainer).html(error);
                }
            }
        }
        $.ajax(cfg);
        
    }
    static handleDALFileUpload(that){
        let input = $(that).parents(".form-group").find("input[type='file']")[0];
        if(input.files.length == 0){
            SYS.dialog("No file provided","Missing file");
        }
        let fd = new FormData();
        fd.append("key","DALImageUpload");
        fd.append("image",input.files[0]);
        SYS.xhr_post(null,fd,"text","no",function(r,o){
            console.log(r);
            r = JSON.parse(r);
            if(r.result == true){
                $(that).parents(".form-group").find("input[type='hidden']").val(r.name);
                $(that).parents(".form-group").find(".image-view-thumbnail").html(`
                    <img height=50 src="${r.url}" />
                    <p>this is a thumbnail, 
                     <a href="${r.url}" target="_blank" > see full image </a> </p>
                `);
            }
            else{
                $(that).parents(".form-group").find(".image-view-thumbnail").html(`${r.msg}`);
            }
            
        });
    }
    static dialog(message,title="Notice",color="grey"){
        if(!$.dialog){
            alert(message);
            return;
        }
        $(`<div title="${title}" >${message}</div>`).dialog({
            modal: true,
            open: function(){
                //jQuery('.ui-dialog-titlebar').css("background",color);
                jQuery('.ui-dialog-titlebar-close')
                .html(`<i class="fa fa-window-close"></i>`)
                .css("padding",0).css("border","none")
                ;
                //var closeBtn = jQuery('.ui-dialog-titlebar-close'); //fix dialog x button display
                //closeBtn.html('<i class="fa fa-eye"></i>');
              },
            buttons: {
              OK: function() {
                $( this ).dialog( "close" );
              }
            }
        });
    }
    static py_read_pixels(out,id,image){
        let fd = new FormData();
        fd.append("id",id);
        fd.append("image",image);
        SYS.xhr_post(`${BASE_DIR}API/read_pixels.py`,fd,"text",out,function(r,o){
            $(`#${o}`).html(r);
        });
    }
    static getIdForColor(color){
        if(SYS.ColorIDtoNameMap == undefined){
            let map = [];
            for(let i in SYS.COLORNAMES){
                map[SYS.COLORNAMES[i].name] = SYS.COLORNAMES[i].id;
            }
            SYS.ColorIDtoNameMap = map;
        }
        return SYS.ColorIDtoNameMap[color];
    }
    static getColorNameForId(id){
        if(SYS.ColorNametoIdMap == undefined){
            let map = [];
            for(let i in SYS.COLORNAMES){
                map[SYS.COLORNAMES[i].id] = SYS.COLORNAMES[i].name;
            }
            SYS.ColorNametoIdMap = map;
            return map[id];
        }
        else{
            return SYS.ColorNametoIdMap[id];
        }
        
    }
    static getColorNamesAutoComplete(){
        if(SYS.ColorNamesAutoComplete == undefined){
            let names = [];
            for(let i in SYS.COLORNAMES){
                names.push({
                    "label" : SYS.COLORNAMES[i].name,
                    "value" : SYS.COLORNAMES[i].name
                });
            }
            SYS.ColorNamesAutoComplete = names;
        }
        return SYS.ColorNamesAutoComplete;
    }
    static getColorsForRGB(rgbId){
        if(!SYS.ColorsForRGB){
            let list = [];
            for(let i in SYS.RGBACOLORMAP){
                let rgba_list_fk = SYS.RGBACOLORMAP[i].rgba_list_fk;
                let colors_names_fk = SYS.RGBACOLORMAP[i].colors_names_fk;
                let color = SYS.getColorNameForId(colors_names_fk);
                if(!list[rgba_list_fk]) list[rgba_list_fk]=[];
                if(color != undefined) list[rgba_list_fk].push(color);
            }
            SYS.ColorsForRGB = list;
        }
        return SYS.ColorsForRGB[rgbId];
    }
    static addRGBColorMap(that){
        let rgbid = $(that).parents("tr").attr("data-rgbid");
        let color_name = $(that).parents("tr").find("input").val();
        if(color_name == ""){
            alert("must enter color");return;
        }
        let color_id = SYS.getIdForColor(color_name);
        if(color_id == undefined){
            alert("misformed color not in existing list");return;
        }
        let c = confirm("sure to add this color ?");
        if(c){
            let fd = new FormData();
            fd.append("key","colors/add_color_rgb_map");
            fd.append("rgbid",rgbid);
            fd.append("color",color_id);
            SYS.xhr_post(null,fd,"text","no",function(r,o){
                console.log(r);
                $(`<p>${r}</p>`).insertAfter(that);
                $(that).parents("tr").find("input").val("");
                $(that).parents("tr").find(".colors").append("<br>"+color_name);
                SYS.ColorsForRGB[rgbid];
                if(!SYS.ColorsForRGB[rgbid]) SYS.ColorsForRGB[rgbid]=[];
                SYS.ColorsForRGB[rgbid].push(color_name);
            });
        }
    }
    static initializeRGBTable(container){
        $(container).empty();
        $(container).html(`<table id="imgcoltbl" class="table table-striped">
        <thead>
        <th>sample</th>
        <th>colors</th>
        <th>color to suggest</th>
        <th>send</th>
        </thead>
        <tbody></tbody>
        </table>`);

        for(let i in SYS.RGBALISTFORIMAGE){
            let rgba = SYS.RGBALISTFORIMAGE[i];
            let colors = SYS.getColorsForRGB(rgba.id);
            if(colors == undefined){
                colors = "";
            }
            let html = `<tr data-rgbid="${rgba.id}" >`;
            html += `<td> 
                <div class="sample" style="background:rgb(${rgba.r},${rgba.g},${rgba.b});" >TEST</div> 
            </td>`;
            html += `<td class="colors" > ${colors} </td>`;
            html += `<td> <input type="text" placeholder="suggested color" /> </td>`;
            html += `<td> <button class="btn" onclick="SYS.addRGBColorMap(this);" >add</button> </td>`;
            html += `</tr>`;
            $(`${container} tbody`).append(html);
        }
        $(`${container} tbody input`).on('focus',function(){
            $(this).autocomplete({
                source : function(request, response) {
                    var results = $.ui.autocomplete.filter(SYS.getColorNamesAutoComplete(), request.term);
                    response(results.slice(0, 10));
                }
            });
        });
        $(`${container} #imgcoltbl`).DataTable();
    }
}