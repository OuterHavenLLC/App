var DefaultContainer = ".Content",
      Inputs = "input, number, select, textarea",
      Language = "[App.Language]",
      Loading = "&bull; &bull; &bull;",
      base = "[App.Base]/?_API=Web&";
function AddContent() {
 var Daemon = function() {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      if($(".AddContent").is(":visible")) {
       $(".AddContent").fadeOut(500);
       setTimeout(function() {
        $(".AddContent").remove(); 
       }, 600);
      }
     } else {
      if(!$(".AddContent").is(":visible")) {
       $("body").append(Response);
       setTimeout(function() {
        $(".AddContent").fadeIn(500);
       }, 500);
      }
     }
    }
   },
   url: base + $.b64.d("[App.AddContent]")
  });
 }
 Daemon();
 setInterval(function() {
  Daemon();
 }, 6000);
}
function AnimateParentToView(Container, Container2, Data = "") {
 var Parent = $("." + Container).parent();
 $(Parent).append("<div class='" + Container2 + " h scr'></div>");
 $(Parent).find("." + Container).fadeOut(500);
 setTimeout(function() {
  if(Data !== "" && typeof(Data) !== "undefined") {
   $(Parent).find("." + Container2).html(Data);
  }
  $(Parent).find("." + Container2).show("slide", {
   direction: "right"
  }, 500);
 }, 600);
}
function Bulletins() {
 var Daemon = function() {
  var Bulletins = ".Bulletins";
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else if(Response > 0) {
      Bulletin = $.b64.d("[App.Bulletin]");
      Bulletin = Bulletin.replaceAll("[Bulletin.Date]", "Now");
      Bulletin = Bulletin.replaceAll("[Bulletin.From]", "[App.Name]");
      Bulletin = Bulletin.replaceAll("[Bulletin.ID]", "NewBulletins");
      Bulletin = Bulletin.replaceAll("[Bulletin.Message]", "You have " + Response + " new Bulletins!");
      Bulletin = Bulletin.replaceAll("[Bulletin.Options]", "<button class='CloseBulletins v2 v2w'>Okay</button>");
      Bulletin = Bulletin.replaceAll("[Bulletin.Picture]", "<img class='c2' src='[Media:LOGO]' style='width:100%'/>");
      $(Bulletins).html(Bulletin);
      setTimeout(function() {
       $(Bulletins).show("slide", {direction: "right"}, 500);
       setTimeout(function() {
        $(Bulletins).hide("slide", {direction: "right"}, 500);
        setTimeout(function() {
         $(Bulletins).empty();
        }, 5000);
       }, 10000);
      }, 500);
     }
    }
   },
   url: base + $.b64.d("[App.Bulletins]")
  });
 };
 Daemon();
 setInterval(function() {
  Daemon();
 }, 120000);
}
function Card(data) {
 var Data = data || {},
       Action = Data["Action"] || "",
       Front = Data["Front"] || "",
       Card = "",
       ID = Data["ID"] || Date.now();
 $(".CloseCard, .OpenCard").each(function() {
  this.disabled = true;
 });
 $("body").append("<div class='CardOverlay CardOverlay" + ID + " Overlay h'></div>");
 $(".CardOverlay" + ID).fadeIn(500);
 Card = "<div class='ToggleAnimation'></div>\r\n";
 Card += "<div class='CardFront Frosted Rounded ShadowedLarge scr'>\r\n";
 Card += "<div class='CardHeader'>\r\n";
 Card += "<div class='Desktop50'>\r\n";
 Card += "<button class='CardButton CloseCard' data-id='" + ID + "'>Close</button>\r\n";
 Card += "</div>\r\n";
 Card += "<div class='Desktop50 RightText'>\r\n";
 Card += Action + "\r\n";
 Card += "</div>\r\n";
 Card += "</div>\r\n";
 Card += "<div class='CardCC'>\r\n";
 Card += Front + "\r\n";
 Card += "</div>\r\n";
 Card += "</div>";
 $(".CardOverlay" + ID).html(Card);
 $(".CardOverlay" + ID + " .CardFront").fadeIn(500);
 $(".CardOverlay" + ID).find(".ToggleAnimation").slideUp(500);
 setTimeout(function() {
  $(".CloseCard, .OpenCard").each(function() {
   this.disabled = false;
  });
 }, 600);
}
function CloseCard(ID) {
 var ID = ID || "",
     Overlay = ".CardOverlay" + ID;
 if(ID === "" || typeof(ID) === "undefined") {
  Overlay = ".CardOverlay:last"
 }
 $(".CloseCard, .OpenCard").each(function() {
  this.disabled = true;
 });
 $(Overlay).find(".ToggleAnimation").slideDown(500);
 $(Overlay).fadeOut(500);
 setTimeout(function() {
  $(".CloseCard, .OpenCard").each(function() {
   this.disabled = false;
  });
  $(Overlay).fadeOut(500);
  setTimeout(function() {
   $(Overlay).remove();
  }, 600);
 }, 600);
}
function CloseDialog(ID) {
 var ID = ID || "",
     Overlay = ".DialogOverlay" + ID;
 if(ID === "" || typeof(ID) === "undefined") {
  Overlay = ".DialogOverlay:last"
 }
 $(".CloseDialog, .OpenDialog").each(function() {
  this.disabled = true;
 });
 $(Overlay + " .Dialog").hide("scale");
 setTimeout(function() {
  $(".CloseDialog, .OpenDialog").each(function() {
   this.disabled = false;
  });
  $(Overlay).fadeOut(500);
  setTimeout(function() {
   $(Overlay).remove();
  }, 600);
 }, 600);
}
function CloseFirSTEPTool(ID) {
 var ID = ID || "",
       FST = ".FST" + ID;
 if(ID === "" || typeof(ID) === "undefined") {
  FST = ".FST:last"
 }
 $(".CloseFirSTEPTool, .OpenFirSTEPTool").each(function() {
  this.disabled = true;
 });
 $(FST).hide("slide", {direction: "right"}, 500);
 setTimeout(function() {
  $(".CloseFirSTEPTool, .OpenFirSTEPTool").each(function() {
   this.disabled = false;
  });
  $(FST).remove();
 }, 600);
}
function CloseNetMap() {
 if($(".NetMap").is(":visible")) {
  $(".CloseNetMap, .OpenNetMap").each(function() {
   this.disabled = true;
  });
  $(".NetMap .ToggleAnimation").slideDown(500);
  setTimeout(function() {
   $(".CloseNetMap, .OpenNetMap").each(function() {
    this.disabled = false;
   });
   $(".NetMap").fadeOut(500);
   setTimeout(function() {
    $(".NetMap").remove();
   }, 500);
  }, 500);
 }
}
function Crash(data) {
 var Data = data || "No Data";
 Dialog({
  "Body": "An internal error has ocurred and the request could not be completed. Please refer to the console for more information on this error.",
  "Header": "Crash Report",
  "Scrollable": Data
 });
}
function LightSearch(input) {
 var Input = input || {},
       List = $(Input).attr("data-list") || "",
       ListContainer = $(Input).attr("data-container") || "";
 if(List === "" || typeof(List) === "undefined") {
  Alert({
   "Body": "Light Search requires a list to serach from."  
  });
 } else {
  if(ListContainer === "" || typeof(ListContainer) === "undefined") {
   Alert({
    "Body": "Light Search requires a container in which to render the list."  
   });
  } else {
   $.ajax({
    data: {
     "Query": $(Input).val()
    },
    headers: {
     Language: $.b64.e(LocalData("Get", "Language")),
     Token: $.b64.e(LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: function(data) {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      Crash(data);
      return false;
     } else {
      var Data = RenderView(data),
            AccessCode = Data["AccessCode"],
            Response = Data["Response"];
      if(AccessCode === "Denied") {
       Dialog(Response);
      } else {
       $(ListContainer).html(Response);
      }
     }
    },
    url: base + $.b64.d(List)
   });
  }
 }
}
function LiveView(input) {
 var Daemon = function() {
  var Input = $(document).find(input),
        DLC = $(Input).val(),
        Preview = $(Input).attr("data-preview-destination"),
        Quantity = $(Input).attr("data-quantity") || $.b64.e("Single");
  if($(Input).length) {
   $.ajax({
    headers: {
     Language: $.b64.e(LocalData("Get", "Language")),
     Token: $.b64.e(LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: function(data) {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      Crash(data);
      return false;
     } else {
      var Data = RenderView(data),
            AccessCode = Data["AccessCode"],
            Response = Data["Response"];
      if(AccessCode === "Denied") {
       Dialog(Response);
      } else {
       $(Preview).html(Response);
      }
     }
    },
    url: base + $.b64.d($(Input).attr("data-live-view")) + DLC + "&PreviewQuantity=" + Quantity
   });
  }
 };
 Daemon();
 setInterval(function() {
  Daemon();
 }, 15000);
}
function LocalData(action = "Get", identifier = "", data = {}) {
 if(action === "Get") {
  if(window.localStorage.getItem(identifier)) {
   if(identifier !== "" && typeof(identifier) !== "undefined") {
    data = window.localStorage.getItem(identifier) || "";
    data = JSON.parse(data);
   }
  } else {
   data = "";
  }
  return data;
 } else if(action === "Purge") {
  if(identifier === "" || typeof(identifier) === "undefined") {
   window.localStorage.clear();
  } else {
   window.localStorage.removeItem(identifier);
  }
 } else if(action === "Save") {
  if(data !== {} && identifier !== "" && typeof(identifier) !== "undefined") {
   data = JSON.stringify(data);
   window.localStorage.setItem(identifier, data);
  }
 }
}
function DeleteContainer(button) {
 var Button = button,
       Container = $(Button).closest($(Button).attr("data-target"));
 $(Container).slideUp(500);
 setTimeout(function() {
  $(Container).remove();
 }, 500);
}
function Dialog(data) {
 var Data = data || {},
       Actions = "",
       ActionsList = Data["Actions"] || "",
       Body = Data["Body"] || "",
       Dialog = "",
       Header = Data["Header"] || "Error",
       ID = Data["ID"] || Date.now(),
       NoClose = Data["NoClose"] || 0,
       Scrollable = Data["Scrollable"] || "";
 $(".CloseDialog, .OpenDialog").each(function() {
  this.disabled = true;
 });
 $("body").append("<div class='DialogOverlay DialogOverlay" + ID + " Overlay h'></div>");
 $(".DialogOverlay" + ID).fadeIn(500);
 if(ActionsList !== "" && typeof(ActionsList) !== "undefined") {
  $(ActionsList).each(function(key, value) {
   Actions += value;
  });
 } if(NoClose === 0) {
  Actions += "<button class='CloseDialog v2 v2w' data-id='" + ID + "'>Cancel</button>\r\n";
 } setTimeout(function() {
  Dialog = "<div class='Frosted Dialog Rounded Shadowed h scr'>\r\n";
  if(Header !== "" && typeof(Header) !== "undefined") {
   Dialog += "<h3 class='CenterText'>" + Header + "</h3>\r\n";
  } if(Body !== "" && typeof(Body) !== "undefined") {
   Dialog += "<p class='CenterText'>" + Body + "</p>\r\n";
  } if(Scrollable !== "" && typeof(Scrollable) !== "undefined") {
   Dialog += "<div class='NONAME scr' style='max-height:400px'>\r\n";
   Dialog += Scrollable + "\r\n";
   Dialog += "</div>\r\n";
  }
  Dialog += Actions + "\r\n";
  Dialog += "</div>";
  $(".DialogOverlay" + ID).html(Dialog);
  $(".DialogOverlay" + ID + " .Dialog").show("scale");
  $(".CloseDialog, .OpenDialog").each(function() {
   this.disabled = false;
  });
 }, 600);
}
function Encode(data) {
 if($.isArray(data) && typeof(data) !== "undefined") {
  $.each(data, function(key, input) {
   input.value = $.b64.e(encodeURIComponent(input.value));
  });
  return data;
 } else {
  Dialog({
   "Body": "Encoder expects a populated list."
  });
 }
}
function FST(data) {
 var Data = data || {},
       ID = Data["ID"] || Date.now();
 $("body").append("<div class='Frosted FST FST" + ID + " RoundedLarge Shadowed h scr'></div>");
 $(".FST" + ID).html(Data);
 setTimeout(function() {
  $(".FST" + ID).show("slide", {
   direction: "right"
  }, 500);
 }, 600);
}
function InstantSignOut() {
 setTimeout(function() {
  LocalData("Purge", "SecurityKey");
 }, 1000);
}
function OpenCard(View) {
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var Data = RenderView(data),
          AccessCode = Data["AccessCode"],
          Response = Data["Response"];
    if(AccessCode === "Denied") {
     Dialog(Response);
    } else {
     Card(Response);
    }
   }
  },
  url: base + $.b64.d(View)
 });
}
function OpenDialog(View) {
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var Data = RenderView(data),
          AccessCode = Data["AccessCode"],
          Response = Data["Response"];
    Dialog(Response);
   }
  },
  url: base + $.b64.d(View)
 });
}
function OpenFirSTEPTool(Ground, FirSTEPTool) {
 if(Ground !== "" && typeof(Ground) !== "undefined") {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else {
      $(DefaultContainer).html(Response);
     }
    }
   },
   url: base + $.b64.d(Ground)
  });
 } if(FirSTEPTool !== "" && typeof(FirSTEPTool) !== "undefined") {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else {
      FST(Response);
     }
    }
   },
   url: base + $.b64.d(FirSTEPTool)
  });
 }
}
function OpenNetMap(a) {
 $("body").append("<div class='Frosted NetMap h scr'></div>");
 $(".CloseNetMap, .OpenNetMap").each(function() {
  this.disabled = true;
 });
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var Data = RenderView(data),
          AccessCode = Data["AccessCode"],
          Response = Data["Response"];
    if(AccessCode === "Denied") {
     Dialog(Response);
    } else {
     $(".NetMap").html(Response);
     setTimeout(function() {
      $(".CloseNetMap, .OpenNetMap").each(function() {
       this.disabled = false;
      });
      $(".NetMap").fadeIn(500);
      $(".NetMap .ToggleAnimation").slideUp(1000);
     }, 500);
    }
   }
  },
  url: base + $.b64.d(a)
 });
}
function RenderDesignView(container) {
 var Container = container || {},
       DesignView = $($(Container).attr("data-in")).val();
 if($(Container).is(":visible")) {
  DesignView = $.b64.e(encodeURIComponent(DesignView));
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else {
      $(Container).html(Response);
      $(Container).find("button, input, select, textarea").each(function() {
       this.disabled = true;
      });
     }
    }
   },
   url: base + $.b64.d($(Container).attr("data-u")) + DesignView
  });
 }
}
function RenderInputs(Container, Data) {
 var Container = Container || DefaultContainer,
     Data = Data || {};
 if(Container !== "" && Data !== {}) {
  $(Container).html("");
  $.each(Data, function(key, input) {
   var Attributes,
       Input = input || {},
       OptionGroup = Input["OptionGroup"] || {},
       OptionGroupLabel,
       Options = Input["Options"] || {},
       Type = Input["Type"] || "Text";
   Attributes = Input["Attributes"] || {};
   setTimeout(function() {
    if(Attributes !== {} && Type !== "") {
     var RenderInput = "",
         RenderInputAttributes = "",
         RenderOptionGroup = "";
     if(Type !== "Select") {
      $.each(Attributes, function(attribute, value) {
       RenderInputAttributes += " " + attribute + "='" + value + "'";
      });
     } if(Type === "Check") {
      Selected = (Options["Selected"] === 1) ? " checked" : "";
      RenderInput = "<div class='NONAME'></div>\r\n";
      RenderInput += "<div class='InnerMargin'>\r\n";
      RenderInput += "<div class='Desktop33'>\r\n";
      RenderInput += "<input" + RenderInputAttributes + " type='checkbox' value='" + Input["Value"] + "'" + Selected + "/>\r\n";
      RenderInput += "</div>\r\n";
      RenderInput += "<div class='Desktop66'>\r\n";
      RenderInput += "<p>" + Input["Text"] + "</p>\r\n";
      RenderInput += "</div>\r\n";
      RenderInput += "</div>\r\n";
     } else if(Type === "Select") {
      if(OptionGroup !== {}) {
       $.each(OptionGroup, function(option, text) {
        Selected = (Input["Value"] === option) ? " selected" : "";
        RenderOptionGroup += "<option value='" + option + "'" + Selected + ">" + text + "</option>\r\n";
       });
      }
      OptionGroupLabel = Options["HeaderText"] || Input["Title"];
      RenderInput = "<select class='LI v2 v2w' name='" + Input["Name"] + "'>\r\n";
      RenderInput += "<optgroup label='" + OptionGroupLabel + "'>\r\n";
      RenderInput += RenderOptionGroup + "\r\n";
      RenderInput += "</optgroup>\r\n";
      RenderInput += "</select>\r\n";
     } else if(Type === "Text") {
      var TextType = Attributes["type"] || "",
          TextValue = (TextType === "hidden") ? Input["Value"] : $.b64.d(Input["Value"]);
      RenderInput = "<input" + RenderInputAttributes + " value='" + TextValue + "'/>\r\n";
     } else if(Type === "TextBox") {
      RenderInput = "<textarea " + RenderInputAttributes + ">" + $.b64.d(Input["Value"]) + "</textarea>\r\n";
      if(Options["WYSIWYG"] === 1) {
       RenderInput = "<textarea " + RenderInputAttributes + " rows='40'>" + $.b64.d(Input["Value"]) + "</textarea>\r\n";
       $.ajax({
        headers: {
         Language: $.b64.e(LocalData("Get", "Language")),
         Token: $.b64.e(LocalData("Get", "SecurityKey"))
        },
        method: "POST",
        success: function(data) {
         if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
          Crash(data);
          return false;
         } else {
          var WData = RenderView(data),
                AccessCode = WData["AccessCode"],
                WYSIWYG = WData["Response"];
          if(AccessCode === "Denied") {
           Dialog(WYSIWYG);
          } else {
           WYSIWYG = WYSIWYG.replaceAll("[WYSIWYG.ID]", Attributes["id"]);
           WYSIWYG = WYSIWYG.replaceAll("[WYSIWYG.TextBox]", RenderInput);
           RenderInput = WYSIWYG;
          }
         }
        },
        url: base + $.b64.d("[App.WYSIWYG]")
       });
       RenderInput += "<div class='NONAME'></div>\r\n";
      }
     } if(Options["Header"] === 1) {
      RenderInput = "<h4 class='UpperCase'>" + Options["HeaderText"] + "</h4>\r\n" + RenderInput + "\r\n";
     } if(Options["Container"] === 1) {
      RenderInput = "<div class='" + Options["ContainerClass"] + "'>" + RenderInput + "</label>\r\n";
     } if(Options["Label"] === 1) {
      RenderInput = "<label>" + RenderInput + "</label>\r\n";
     }
    }
    $(Container).append(RenderInput);
   }, 500);
  });
 }
}
function RenderView(data) {
 var Data = JSON.parse($.b64.d(data)),
       AccessCode = Data["AccessCode"] || "Denied",
       Response = Data["Response"]["Web"] || "",
       ResponseType = Data["ResponseType"] || "Dialog",
       Success = Data["Success"] || "",
       Title = Data["Title"] || "[App.Name]";
 $(document).prop("title", Title);
 return {
  "AccessCode": AccessCode,
  "Response": Response,
  "ResponseType": ResponseType,
  "Success": Success
 };
}
function RenderVisibilityFilter(Container, Data) {
 var Container = Container || DefaultContainer,
       Data = Data || {},
       Response = {};
 if(Data !== {}) {
  var Filter = Data["Filter"] || "Privacy",
      Name = Data["Name"] || "Privacy",
      OptionGroup = {},
      Title = Data["Title"] || "Content Visibility",
      Value = Data["Value"] || "";
  if(Filter === "NSFW") {
   OptionGroup = {
    "0": "Kid-Friendly",
    "1": "Adults Only"
   };
  } else if(Filter === "Privacy") {
   OptionGroup = {
    "55c53cfda992192581cb4f006109df47": "Acquaintances",
    "43b5ac258be80f9a8f5bc8d3c6036e2b": "Close Contacts",
    "9aa698f602b1e5694855cee73a683488": "Contacts",
    "47f9082fc380ca62d531096aa1d110f1": "Private",
    "3d067bedfe2f4677470dd6ccf64d05ed": "Public"
   };
  }
  RenderInputs(Container, [
   {
    "Attributes": {},
    "OptionGroup": OptionGroup,
    "Options": {
     "Container": 1,
     "ContainerClass": "NONAME",
     "Header": 1,
     "HeaderText": Title
    },
    "Name": Name,
    "Title": Title,
    "Type": "Select",
    "Value": Value
   }
  ]);
 }
}
function RenderVisibilityFilters(Container, Data) {
 var Container = Container || DefaultContainer,
       Data = Data || {},
       Inputs = [],
       Response = {};
 if(Data !== {}) {
  $.each(Data, function(key, input) {
   var Filter = input["Filter"] || "Privacy",
       Name = input["Name"] || "Privacy",
       OptionGroup = {},
       Title = input["Title"] || "Content Visibility",
       Value = input["Value"] || "";
   if(Filter === "NSFW") {
    OptionGroup = {
     "0": "Kid-Friendly",
     "1": "Adults Only"
    };
   } else if(Filter === "Privacy") {
    OptionGroup = {
     "55c53cfda992192581cb4f006109df47": "Acquaintances",
     "43b5ac258be80f9a8f5bc8d3c6036e2b": "Close Contacts",
     "9aa698f602b1e5694855cee73a683488": "Contacts",
     "47f9082fc380ca62d531096aa1d110f1": "Private",
     "3d067bedfe2f4677470dd6ccf64d05ed": "Public"
    };
   }
   Inputs.push({
    "Attributes": {},
    "OptionGroup": OptionGroup,
    "Options": {
     "Container": 1,
     "ContainerClass": "Desktop50 MobileFull",
     "Header": 1,
     "HeaderText": Title
    },
    "Name": Name,
    "Title": Title,
    "Type": "Select",
    "Value": Value
   });
  });
  RenderInputs(Container, Inputs);
 }
}
function Search(input) {
 var Input = input || {},
       ListContainer = $(Input.Bar).attr("data-container") || "",
       Refresh = Input.Refresh || "On",
       RefreshRate = Input.RefreshRate || 15000;
 check = ($.type(Input) === "array" || $.type(Input) === "object") ? 1 : 0;
 check2 = ($.type(Input) !== "undefined") ? 1 : 0;
 if(check === 0 || check2 === 0) {
  console.log("No parameters supplied.");
  return false;
 } else {
  function Daemon(data) {
   var Bar = data.Bar || "",
         Container = $(Bar).attr("data-container");
         Query = $(Bar).val() || "";
   if(Bar === "" || typeof(Bar) === "undefined") {
    console.log("The Search Bar is missing.");
    return false;
   } else if(Container === "" || typeof(Container) === "undefined") {
    return false;
   } else {
    var Container = $(document).find(Container),
          List = $(Bar).attr("data-list");
    $.ajax({
     headers: {
      Language: $.b64.e(LocalData("Get", "Language")),
      Token: $.b64.e(LocalData("Get", "SecurityKey"))
     },
     method: "POST",
     success: function(data) {
      if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
       Crash(data);
       return false;
      } else {
       var data = JSON.parse($.b64.d(data)),
             Response = "";
       $(Container).html($.b64.d(data[3]));
       if(data[0] === "Accepted") {
        var List = getSortedList(JSON.parse($.b64.d(data[1]))),
              ListItems = 0,
              check = ($.type(List) !== "undefined" && List !== {}) ? 1 : 0,
              check = ($.type(List) === "object" || check === 1) ? 1 : 0;
        $(Container).empty();
        if(check === 1) {
         check = (Query !== "" && typeof(Query) !== "undefined") ? 1 : 0;
         for(var i in List) {
          var LIC = ($.type(List[i][0]) !== "undefined") ? 1 : 0,
                LIC2 = ($.type(List[i][1]) !== "undefined") ? 1 : 0;
          if(LIC === 0 || LIC2 === 0) {
           console.log("The Key or value for " + i + " was empty.");
          } else {
           var Extension = $.b64.d(data[2]),
                 Search = (check === 0) ? 1 : 0,
                 SearchProbe = "",
                 value = List[i][1] || {};
           if(value !== {} && $.type(value) !== "undefined") {
            for(var i in value) {
             Extension = Extension.replaceAll(value[i][0], $.b64.d(value[i][1]));
            } if(Extension.search(Query) > -1) {
             Search = Search + 1;
            } if(Extension.toLowerCase().search(Query.toLowerCase()) > -1) {
             Search = Search + 1;
            } if(Search > 0) {
             ListItems = ListItems + 1;
             $(Container).append(Extension);
            }
           }
          }
         } if(ListItems < 1) {
          $(Container).html("<p class='CenterText'>" + $.b64.d(data[3]) + "</p>");
         }
        } else {
         $(Container).html("<p class='CenterText'>" + $.b64.d(data[3]) + "</p>");
        }
       }
      }
     },
     url: base + $.b64.d(List) + "&query=" + $.b64.e(Query)
    });
   }
  }
  Daemon(Input);
  if(Refresh === "On") {
   if(ListContainer !== "" || typeof(ListContainer) !== "undefined") {
    ListContainer = $(document).find(ListContainer);
    setInterval(function() {
     if($(ListContainer).is(":visible")) {
      Daemon(Input);
     }
    }, RefreshRate);
   }
  }
 }
}
function SignIn() {
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var Data = RenderView(data),
          AccessCode = Data["AccessCode"],
          Response = Data["Response"];
    if(AccessCode === "Denied") {
     Dialog(Response);
    } else {
     $(DefaultContainer).html(Response);
     CloseFirSTEPTool();
     setTimeout(function() {
      $(document).keyup(function(e) {
       if(e.keyCode === 27) {
        if(e.shiftKey) {
         Locheck();
        }
       }
      });
     }, 500);
    }
   }
  },
  url: base + $.b64.d("[App.MainUI]")
 });
}
function SignOut() {
 InstantSignOut();
 setTimeout(function() {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else {
      $(DefaultContainer).html(Response).prepend("<div class='TopBarMargin'></div>");
      UpdateContent(".Menu", "[App.Menu]");
      $(".Menu").slideUp(500);
     }
    }
   },
   url: base + $.b64.d("[App.OptIn]")
  });
 }, 600);
}
function UpdateButton(button, data) {
 var Attributes,
     Button = button || {},
     Data = data || {},
     Text = $(button).text() || "Error";
 if(Button !== {} && Data !== {}) {
  Attributes = Data["Attributes"] || {};
  Text = Data["Text"] || Text;
  if(Attributes !== {}) {
   $.each(Attributes, function(key, value) {
    $(Button).attr(key, value);
   });
  }
  $(Button).html(Text);
 }
}
function UpdateContent(Container, View, Debug = 0) {
 var Container = Container || DefaultContainer;
 $(Container).html("<h4 class='CenterText InnerMargin'>&bull; &bull; &bull;</h4>\r\n");
 setTimeout(function() {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else {
      $(Container).html(Response);
      if(Container === DefaultContainer) {
       $(Container).prepend("<div class='TopBarMargin'></div>");
      }
     }
    }
   },
   url: base + $.b64.d(View)
  });
 }, 600);
}
function UpdateContentRecursive(Container, View, Interval) {
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var Data = RenderView(data),
          AccessCode = Data["AccessCode"],
          Response = Data["Response"];
    if(AccessCode === "Denied") {
     Dialog(Response);
    } else {
     $(Container).html(Response);
     if(Container === DefaultContainer) {
      $(Container).prepend("<div class='TopBarMargin'></div>");
     }
    }
   }
  },
  url: base + $.b64.d(View)
 });
 if(Interval === "" || typeof(Interval) === "undefined" || Interval < 15000) {
  var Interval = 15000;
 } setInterval(function() {
  if($(Container).is(":visible")) {
   $.ajax({
    headers: {
     Language: $.b64.e(LocalData("Get", "Language")),
     Token: $.b64.e(LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: function(data) {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      Crash(data);
      return false;
     } else {
      var Data = RenderView(data),
            AccessCode = Data["AccessCode"],
            Response = Data["Response"];
      if(AccessCode === "Denied") {
       Dialog(Response);
      } else {
       $(Container).html(Response);
      }
     }
    },
    url: base + $.b64.d(View)
   });
  }
 }, Interval);
}
function UpdateCoverPhoto(Container, Photo) {
 var Container = Container || DefaultContainer,
     Photo = Photo || "";
 if(photo !== "" && typeof(photo) !== "undefined") {
  $(Container).css({
   "bacheckground-image": "https://efs.outerhaven.nyc/" + photo
  });
 }
}
function Upload(Button) {
 var Form = $(Button).attr("data-form"),
       Inputs = "input, number, select, textarea",
       Pass = 1,
       Processor = $(Button).attr("data-processor"),
       Text = $(Button).text();
 $(Button).text(Text);
 $(Button).attr("disabled", true);
 $(Form).find(".req").each(function() {
  $(this).removeClass("Red");
  if($(this).val() === "") {
   $(this).addClass("Red");
   Pass = 0;
  }
 });
 if(Pass === 0) {
  $(Button).text(Text);
  $(Button).attr("disabled", false);
  return false;
 } else {
  var Data = new FormData(),
        Files = $(Form).find(".FileList")[0].files,
        Inputs = $(Form).find(Inputs).serializeArray(),
        Request = new XMLHttpRequest();
  for(var i = 0; i < Files.length; i++) {
   if(Files[i].size > Math.round(500000 * 100)) {
    console.log("The media file " + Files[i].name + " comes in at " + Files[i].size + ". The maximum allowed file size is 500MB.");
   } else {
    Data.append("Uploads[" + i + "]", Files[i]);
    console.log("Added file:");//TEMP
    console.log(Files[i]);//TEMP
   }
  } for(var i = 0; i < Inputs.length; i++) {
   Data.append(Inputs[i].name, $.b64.e(encodeURIComponent(Inputs[i].value)));
   console.log("Added Input:");//TEMP
   console.log(Inputs[i].name + " --> " + Inputs[i].value);//TEMP
  }
  Request.upload.addEventListener("progress", function(event) {
   var Percent = Math.round((event.loaded / event.total) * 100),
         ProgressContainer = "";
   ProgressContainer += "<progress class='ProgressBar' max='100' value='" + Percent + "'></progress>\r\n";
   ProgressContainer += "<h4 class='ProgressText'>" + Percent + "% uploaded... Please wait.</h4>\r\n";
   $(".Uploads").html(ProgressContainer);
  }, false);
  Request.addEventListener("load", function(event) {
   var data = event.target.responseText;
   console.log(data);//TEMP
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var AccessCode = "Denied",
          Class,
          Passed,
          Response,
          Success,
          Type = "Dialog",
          data = JSON.parse($.b64.d(data));
    AccessCode = data["AccessCode"] || AccessCode;
    Response = data["Response"]["JSON"] || {};
    Success = data["Success"] || "";
    Type = data["ResponseType"] || Type;
    if(Response === "" || typeof(Response) === "undefined") {
     Dialog({
      "Body": "<em>[App.Name]</em> returned an empty response. Check the processor within the following URI fragment: " + Processor + "."
     });
    } else {
     if(AccessCode === "Denied") {
      Dialog({
       "Body": "The upload was rejected, please refer to the console."
      });
      console.log(Response);
     } else {
      Passed = Response["Passed"] || {};
      if(Passed !== {} && typeof(Passed) !== "undefined") {
       $($(Form).find(".EmptyOnSuccess")).each(function(k, v) {
        $(this).val("");
       });
       $(".Uploads").html("").addClass("SideScroll");
       for(var i = 0; i < Passed.length; i++) {
        $(".Uploads").append(Passed[i]["HTML"]);
       }
      }
     }
     $(Button).attr("disabled", false);
     $(Button).text("Upload");
    }
   }
  }, false);
  Request.open("POST", base + $.b64.d(Processor), true);
  Request.setRequestHeader("Language", $.b64.e(LocalData("Get", "Language")));
  Request.setRequestHeader("Token", $.b64.e(LocalData("Get", "SecurityKey")));
  Request.send(Data);
 }
}
function W(link, target) {
 var W = window.open(link, target);
 W.focus();
}
function getCreditExchange(data) {
 setInterval(function() {
  var CreditExchange = $(document).find(".CE" + data),
        Credits,
        Numeric;
  if($(CreditExchange).find(".RI" + data).is(":visible")) {
   Credits = $(CreditExchange).find(".RI" + data).val();
   Numeric = (Credits * 0.00001).toFixed(2);
   $(CreditExchange).find(".CreditExchange").text("Apply $" + Numeric);
   $(CreditExchange).find(".GetRangeValue").text(Credits);
  }
 }, 250);
}
function getFSTvisibility() {
 if($(".FST:visible:last").is(":visible")) {
  CloseFirSTEPTool();
  return false;
 } else {
  return "Accepted";
 }
}
function getEmailValidation(data) {
 var email = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
 return email.test(data);
}
function getRangeValue(data) {
 var Range = $(document).find(data);
 setInterval(function() {
  if($(Range).is(":visible")) {
   $(Range).next().closest(".GetRangeValue").text($(Range).val());
  }
 }, 250);
}
function getSortedList(data) {
 var Response = [];
 $.each(data, function(key, value) {
  if($.type(value) === "object") {
   value = getSortedList(value);
  }
  Response.push([key, value]);
 });
 return JSON.parse(JSON.stringify(Response.sort(function(a, b) {
  if(a[0] === b[0]) {
   Response =  0;
  } else {
   Response = (a[0] < b[0]) ? -1 : 1;
  }
  return Response;
 }).reduce(function(key, value) {
  key[value] = value;
  return key;
 }, {})));
}
function uniqid(prefix = "", more_entropy) {
 if(typeof prefix === "undefined") {
  prefix = "";
 }
 var retId,
       formatSeed = function(seed, reqWidth) {
        seed = parseInt(seed, 10).toString(16);
        if (reqWidth < seed.length) {
         return seed.slice(seed.length - reqWidth);
        } if(reqWidth > seed.length) {
         return Array(1 + (reqWidth - seed.length)).join('0') + seed;
        }
        return seed;
       };
 if(!this.php_js) {
  this.php_js = {};
 } if(!this.php_js.uniqidSeed) {
  this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
 }
 this.php_js.uniqidSeed++;
 retId = prefix;
 retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
 retId += formatSeed(this.php_js.uniqidSeed, 5);
 if(more_entropy) {
  retId += (Math.random() * 10).toFixed(8).toString();
 }
 return retId;
}
$(document).on("change", "select[name='ProductQuantity']", function() {
 var Price = $(this).parent().find(".AddToCart").text(),
       Quantity = $(this).find("option:selected").val();
 Price = Price.replace("$", "");
 Price = parseInt(Price) * parseInt(Quantity);
 $(this).parent().find(".AddToCart").text("$" + Price);
});
$(document).on("click", ".AddTo", function() {
 var DLC = $(this).attr("data-dlc"),
       Input = $(document).find($.b64.d($(this).attr("data-input"))),
       InputType = $(Input).prop("nodeName").toLowerCase(),
       MediaFile = $(Input).val();
 this.disabled = true;
 if(!Input) {
  $(this).text("Failed to find the attachment input.");
 } else {
  if(MediaFile === "" || MediaFile === "Array" || typeof(MediaFile) === "undefined") {
   MediaFile = $.b64.e(DLC + ";");
  } else {
   MediaFile = $.b64.e($.b64.d(MediaFile) + DLC + ";");
  } if(InputType === "div") {
   $(Input).attr("data-list");
  } else {
   $(Input).val(MediaFile);
  }
  $(this).text(Loading);
  CloseCard();
 }
});
$(document).on("click", ".Clone", function() {
 var Button = $(this),
       CloneID = uniqid("Clone"),
       Destination = $(Button).attr("data-destination"),
       Source = $($(Button).attr("data-source")).text();
 Source = $.b64.d(Source.trim());
 Source = Source.replaceAll("[Clone.ID]", CloneID);
 $(Destination).append(Source);
});
$(document).on("click", ".CloseAllCards", function() {
 $(".CardOverlay").each(function() {
  setTimeout(function() {
   CloseCard();
  }, 500);
 });
});
$(document).on("click", ".CloseAllDialogs", function() {
 $(".DialogOverlay").each(function() {
  setTimeout(function() {
   CloseDialog();
  }, 500);
 });
});
$(document).on("click", ".CloseAllFirSTEPTools", function() {
 CloseAllFirSTEPTools();
});
$(document).on("click", ".CloseBottomBar", function() {
 $(".BottomBar").hide("slide", {direction: "down"}, 500);
 setTimeout(function() {
  $(".BottomBar").remove();
 }, 500);
});
$(document).on("click", ".CloseBulletins", function() {
 $(".Bulletins").hide("slide", {direction: "right"}, 500);
 setTimeout(function() {
  $(".Bulletins").empty();
 }, 600);
});
$(document).on("click", ".CloseCard", function() {
 CloseCard($(this).attr("data-id"));
});
$(document).on("click", ".CloseDialog", function() {
 CloseDialog($(this).attr("data-id"));
});
$(document).on("click", ".CloseFirSTEPTool", function() {
 CloseFirSTEPTool($(this).attr("data-id"));
});
$(document).on("click", ".CloseNetMap", function() {
 CloseNetMap();
});
$(document).on("click", ".CreditExchange", function() {
 var Button = $(this),
       F = ".CE" + $(Button).attr("data-id"),
       P = $(Button).attr("data-p");
 P = $(F).find(".RI" + $(Button).attr("data-id")).val();
 if($.isNumeric(P)) {
  $(Button).attr("disabled", "true");
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     Dialog(Response);
     if(AccessCode === "Denied") {
      $(Button).attr("disabled", false);
     }
    }
   },
   url: base + $.b64.d($(Button).attr("data-u")) + $.b64.e(P)
  });
 }
});
$(document).on("click", ".Delete", function() {
 var Button = $(this),
       Processor = $(this).attr("data-processor"),
       Text = $(this).text();
 $(Button).attr("disabled", true);
 $(Button).text("Dispatching...");
 if(Processor === "" || typeof(Processor) === "undefined") {
  DeleteContainer(Button);
 } else {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      $(Button).text("Try Later");
     } else {
      $(Button).text("Done!");
      DeleteContainer(Button);
     }
     Dialog(Response);
    }
   },
   url: base + $.b64.d(Processor)
  });
 }
 setTimeout(function() {
  $(Button).attr("disabled", false);
  $(Button).text(Text);
 }, 6000);
});
$(document).on("click", ".Disable", function() {
 $(this).attr("disabled", true);
});
$(document).on("click", ".Download", function() {
 var Button = $(this),
       Downloader = $(Button).attr("data-view") || "",
       Media = $(Button).attr("data-media") || "";
 if(Media === "" || typeof(Media) === "undefined") {
  Dialog({
   "Body": "No media to download."
  });
 } else {
  Media = $.b64.d(Media).split(";");
  $.each(Media, function(key, value) {
   $.ajax({
    data: {
     FilePath: value
    },
    headers: {
     Language: $.b64.e(LocalData("Get", "Language")),
     Token: $.b64.e(LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: function(blob, status, xhr) {
     var Disposition = xhr.getResponseHeader("Content-Disposition"),
           File = "";
     if(Disposition && Disposition.indexOf("attachment") !== -1) {
      var FileRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            Matches = FileRegex.exec(Disposition);
      if(Matches != null && Matches[1]) {
       File = Matches[1].replace(/['"]/g, "");
       if(typeof window.navigator.msSaveBlob !== "undefined") {
        window.navigator.msSaveBlob(blob, File);
       } else {
        var URL = window.webkitURL || window.URL,
              DownloadURL;
        DownloadURL = URL.createObjectURL(blob);
        if(File) {
         var a = document.createElement("a");
         if(typeof(a.download) === "undefined") {
          window.location.href = DownloadURL;
         } else {
          a.href = DownloadURL;
          a.download = File;
          document.body.appendChild(a);
          a.click();
         }
        } else {
         window.location.href = DownloadURL;
        }
        setTimeout(function() {
         URL.revokeObjectURL(DownloadURL);
        }, 100);
       }
      }
     }
    },
    url: base + $.b64.d(Downloader),
    xhrFields: {
     responseType: "blob"
    }
   });
  });
 }
});
$(document).on("click", ".GoToParent", function() {
 var Data = $(this).attr("data-type"),
     Parent = $(".ParentPage" + Data).parent();
 $(Parent).find(".ViewPage" + Data).fadeOut(500);
 setTimeout(function() {
  $(Parent).find(".ParentPage" + Data).show("slide", {
   direction: "left"
  }, 500);
  setTimeout(function() {
   $(Parent).find(".ViewPage" + Data).remove();
  }, 500);
 }, 500);
});
$(document).on("click", ".GoToView", function() {
 var Data = $(this).attr("data-type").split(";"),
       ID = Data[0],
       Parent = $(".ParentPage" + ID).parent(),
       View = Data[1];
 setTimeout(function() {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else {
      AnimateParentToView("ParentPage" + ID, "ViewPage" + ID, Response);
     }
    }
   },
   url: base + $.b64.d(View)
  });
 }, 500);
});
$(document).on("click", ".InstantSignOut", function() {
 InstantSignOut();
});
$(document).on("click", ".MarkAsRead", function() {
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   }
  },
  url: base + $.b64.d($(this).attr("data-MAR"))
 });
});
$(document).on("click", ".Menu button", function() {
 $(".Menu").slideUp(500);
});
$(document).on("click", ".OpenBottomBar", function() {
 var View = $(this).attr("data-view") || "";
 if(View !== "" && typeof(View) !== "undefined") {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else {
      $("body").append(Response);
      $(".BottomBar").show("slide", {direction: "down"}, 500);
     }
    }
   },
   url: base + $.b64.d(View)
  });
 }
});
$(document).on("click", ".OpenCard", function() {
 var Button = $(this),
       View = $(Button).attr("data-view");
 OpenCard(View);
});
$(document).on("click", ".OpenDialog", function() {
 var Button = $(this),
       View = $(Button).attr("data-view");
 OpenDialog(View);
});
$(document).on("click", ".OpenFirSTEPTool", function() {
 var Button = $(this),
       FST = $(Button).attr("data-fst"),
       Ground = $(Button).attr("data-ground");
 OpenFirSTEPTool(Ground, FST);
});
$(document).on("click", ".PS", function() {
 var Data = $(this).attr("data-type").split(";");
 $($(Data[0]).find(Data[1])).each(function() {
  $(this).fadeOut(500);
 });
 setTimeout(function() {
  $(Data[2]).show("slide", {direction: "right"}, 500);
 }, 600);
});
$(document).on("click", ".PSAccordion", function() {
 var Data = $(this).attr("data-type").split(";");
 $($(Data[0]).find(Data[1])).each(function() {
  $(this).slideUp(500);
 });
 setTimeout(function() {
  $(Data[2]).slideDown(500);
 }, 600);
});
$(document).on("click", ".PSBack", function() {
 var Data = $(this).attr("data-type").split(";");
 $($(Data[0]).find(Data[1])).each(function() {
  $(this).fadeOut(500);
 });
 setTimeout(function() {
  $(Data[2]).show("slide", {direction: "left"}, 500);
 }, 600);
});
$(document).on("click", ".PSPill", function() {
 var Data = $(this).attr("data-type").split(";");
 $($(this).parent(".Pill")).children("button").removeClass("Active");
 $(this).addClass("Active");
 $($(Data[0]).find(Data[1])).each(function() {
  $(this).fadeOut(500);
 });
 setTimeout(function() {
  $(Data[2]).show("slide", {direction: "right"}, 500);
 }, 600);
});
$(document).on("click", ".Reg", function() {
 Language = $(this).attr("data-type") || "[App.Language]";
 LocalData("Save", "Language", Language);
 $(".RegSel").fadeOut(500);
});
$(document).on("click", ".RemoveFromAttachments", function() {
 var Input = $(document).find($(this).attr("data-input")),
       ID = $(this).attr("data-id"),
       Value = $.b64.d($(Input).val());
 if(Value.search(";") > 0) {
  Value = Value.replace(ID + ";", "");
 } else {
  Value = Value.replace(ID, "");
 }
 if(Value === "" || typeof(Value) === "undefined") {
  $(Input).val(Value);
 } else {
  $(Input).val($.b64.e(Value));
 }
});
$(document).on("click", ".ReportContent", function() {
 var Button = $(this),
       ID = $(Button).attr("data-id"),
       Processor = $.b64.d($(Button).attr("data-processor")),
       Type = $.b64.e($(Button).attr("data-type"));
 $(Button).attr("disabled", true);
 if(ID !== "" && typeof(ID) !== "undefined") {
  Processor = Processor.replace("[ID]", ID);
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Response = Data["Response"];
     if(AccessCode === "Denied") {
      Dialog(Response);
     } else {
      CloseCard();
      Dialog(Response);
     }
    }
   },
   url: base + Processor + "&Type=" + Type
  });
 }
});
$(document).on("click", ".SendData", function(event) {
 var Button = $(this),
     Form = $(this).attr("data-form"),
     Pass = 1,
     Processor = $.b64.d($(this).attr("data-processor")),
     Target = $(this).attr("data-target") || Form,
     Text = $(this).text();
 event.preventDefault();
 $(Button).attr("disabled", true);
 $(Button).text(Loading);
 $(Form).find("input[type='email']").each(function() {
  $(this).removeClass("Red");
  if(!getEmailValidation($(this).val())) {
   $(this).addClass("Red");
   Dialog({
    "Body": "The email address format is invalid."
   });
   Pass = 0;
  }
 });
 $(Form).find(".req").each(function() {
  $(this).removeClass("Red");
  if($(this).val() === "") {
   $(this).addClass("Red");
   Pass = 0;
  }
 });
 if(Pass === 0) {
  $(Button).text(Text);
  $(Button).attr("disabled", false);
  return false;
 } else {
  $.ajax({
   data: Encode($(Form).find(Inputs).serializeArray()),
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return false;
    } else {
     var Class,
           Data = RenderView(data),
           AccessCode = Data["AccessCode"],
           Processor = $.b64.d(Processor),
           Response = Data["Response"],
           Success = Data["Success"],
           Type = Data["ResponseType"];
     if(Response === "" || typeof(Response) === "undefined") {
      Dialog({
       "Body": "<em>Outer Haven</em> returned an empty response. Check the following Processor view: " + Processor + "."
      });
     } else {
      if(AccessCode === "Denied") {
       Dialog(Response);
      } else {
       $(Form).find(".EmptyOnSuccess").each(function() {
        $(this).val("");
       });
       $(Form).find(".RestoreDefaultValue").each(function() {
        $(this).val($(this).attr("data-default"));
       });
       if(Success === "CloseCard") {
        CloseCard();
       } else if(Success === "CloseDialog") {
        CloseDialog();
       } if(Form === ".SignIn") {
        LocalData("Save", "SecurityKey", Response);
        $(Form).find(Inputs).val("");
        setTimeout(function() {
         if($(location).attr("href") === "[App.Base]/") {
          CloseDialog();
          SignIn();
         } else {
          location.reload();
         }
        }, 1000);
       } if(Text === "Post") {
        $(Button).text("Update");
       }
       setTimeout(function() {
        if(Type === "Destruct") {
         $(Target).toggle(500);
         setTimeout(function() {
          $(Target).remove();
         }, 600);
        } else if(Type === "Dialog") {
         Dialog(Response);
        } else if(Type === "Card") {
         Card(Response);
        } else if(Type === "GoToView") {
         var Data = {
                0: Form.replace(".ParentPage", ""),
                1: Processor
               },
               Parent = $(".ParentPage" + Data[0]).parent();
         $(Parent).append("<div class='ViewPage" + Data[0] + " h scr'></div>");
         $(Parent).find(".ParentPage" + Data[0]).fadeOut(500);
         setTimeout(function() {
          $(Parent).find(".ViewPage" + Data[0]).html(Response).show("slide", {
           direction: "right"
          }, 500);
         }, 600);
        } else if(Type === "ReplaceContent") {
         $(Target).html(Response);
        } else if(Type === "UpdateButton") {
         UpdateButton(Button, Response);
        } else if(Type === "UpdateText") {
         $(Button).text(Response);
        }
       }, 750);
      }
     }
    } if(Type !== "UpdateButton") {
     $(Button).text(Text);
    }
    $(Button).attr("disabled", false);
   },
   url: base + Processor
  });
 }
});
$(document).on("click", ".ToggleMenu", function() {
 var Content = DefaultContainer;
 if($(".FST").is(":visible")) {
  CloseFirSTEPTool();
 } else {
  if($(".NetMap").is(":visible")) {
   CloseNetMap();
  } else {
   if($(".Menu").is(":visible")) {
    $(".Menu").slideUp(500);
   } else {
    $(".Menu").slideDown(500);
   }
  }
 }
});
$(document).on("click", ".ToggleNetMap", function() {
 if($(".FST").is(":visible")) {
  CloseFirSTEPTool();
 } else {
  if($(".NetMap").is(":visible")) {
   CloseNetMap();
  } else {
   OpenNetMap($(this).attr("data-map"));
  }
 }
});
$(document).on("click", ".Unlock", function() {
 var Form = $(document).find(".Dialog:last"),
       PIN = $.b64.e($(Form).find(".PIN").val()),
       SecurePIN = $(Form).find(".sPIN").val();
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    PIN = $.b64.d(data);
    if(PIN === SecurePIN) {
     CloseDialog();
    } else {
     $(Form).effect("shake").find(".PIN").val("");
    }
   }
  },
  url: base + "v=MD5&MD5=" + PIN
 });
});
$(document).on("click", ".UpdateButton", function() {
 var Button = $(this),
       Processor = $.b64.d($(this).attr("data-processor"));
 $(Button).attr("disabled", true);
 $(Button).text(Loading);
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var Data = RenderView(data),
          AccessCode = Data["AccessCode"],
          Response = Data["Response"];
    if(AccessCode === "Denied") {
     Dialog(Response);
    } else {
     UpdateButton(Button, Response);
     $(Button).attr("disabled", false);
    }
   }
  },
  url: base + Processor
 });
});
$(document).on("click", ".UpdateContent", function() {
 var Button = $(this),
       Container = $(Button).attr("data-container") || DefaultContainer,
       View = $(Button).attr("data-view");
 $(Button).attr("disabled", true);
 setTimeout(function() {
  UpdateContent(Container, View);
 }, 500);
});
$(document).on("click", ".UploadFiles", function(event) {
 var Button = $(this);
 event.preventDefault();
 $(Button).attr("disabled", true);
 $(Button).text(Loading);
 Upload(Button);
});
$(document).on("click", "#logout", function() {
 var Button = $(this);
 $(Button).text("Signing out...");
 SignOut();
});
$(document).on("keyup", ".CheckIfNumeric", function(e) {
 var AllowsSymbols = $(this).attr("data-symbols") || 0,
       Pass,
       Value = $(this).val();
 if(AllowsSymbols === "Y") {
  if(Value !== "" && typeof(Value) !== "undefined") {
   Pass = Value.match(/^([1-9]\d{0,1}(\,\d{3})*|([1-9]\d*))(\.\d{2})?$/);
   if(Pass === null) {
    Dialog({
     "Body": "Enter price only. For example: 523.36 or 1,776.00."
    });
    Value = "";
   }
  }
 } else {
  Value = Value.replace(/\D/g, "");
 }
 $(this).val(Value);
});
$(document).on("keyup", ".DiscountCodes", function() {
 var Data = $.b64.d($(this).attr("data-u")),
       F = $(this).closest(".DiscountCodesCC");
 Data = Data.replace("[DC]", $.b64.e($(this).val()));
 Data = Data.replace("[ID]", $.b64.e($(this).attr("data-id")));
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var data = JSON.parse($.b64.d(data));
    if(data[0] === "Accepted") {
     $(F).html(data[1]);
    } else {
     $(F).find("p.c:last").html(data[1]);
    }
   }
  },
  url: base + Data
 });
});
$(document).on("keyup", ".LightSearch", function() {
 LightSearch($(this));
});
$(document).on("keyup", ".LinkData", function() {
 var Input = $(this),
       Link = $.b64.e($(Input).val()),
       Preview = $.b64.d($(Input).attr("data-preview"));
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var Data = RenderView(data),
          AccessCode = Data["AccessCode"],
          Response = Data["Response"];
    if(AccessCode === "Denied") {
     Dialog(Response);
    } else {
     $(".AddLink > .LinkPreview").html(Response);
    }
   }
  },
  url: base + Preview + "&Link=" + Link
 });
});
$(document).on("keyup", ".TopBar .SearchBar", function() {
 var Bar = $(this),
       Content = DefaultContainer,
       DocumentWidth = $(document).width();
       Query;
 if(getFSTvisibility() === "Accepted") {
  CloseNetMap();
  Query = $.b64.e($(Bar).val());
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    $(Content).html($.b64.d(data));
    $(".Menu").slideUp(500);
   },
   url: base + $.b64.d($(Bar).attr("data-u")) + Query
  });
 }
});
$(document).on("keyup", ".UnlockProtectedContent", function() {
 var Input = $(this),
       Key = $.b64.e($(Input).val()),
       SignOut = $(Input).attr("data-signout") || "",
       Parent = $(Input).closest(".ProtectedContent");
 $.ajax({
  headers: {
   Language: $.b64.e(LocalData("Get", "Language")),
   Token: $.b64.e(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: function(data) {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return false;
   } else {
    var Data = RenderView(data),
          AccessCode = Data["AccessCode"],
          Response = Data["Response"];
    if(AccessCode === "Accepted") {
     $(Input).attr("disabled", true);
     setTimeout(function() {
      if(SignOut === "Yes") {
       InstantSignOut();
      }
      $(Parent).html(Response);
     }, 600);
    }
   }
  },
  url: base + $.b64.d($(Input).attr("data-view")) + "&Key=" + Key
 });
});
$(document).keypress(function(e) {
 var k = e.which;
 if(k === 13) {
  e.preventDefault();
 }
});
$(document).ready(function() {
 if($(location).attr("href") === "[App.Base]/") {
  $.ajax({
   headers: {
    Language: $.b64.e(LocalData("Get", "Language")),
    Token: $.b64.e(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: function(data) {
    if($(location).attr("href") === "[App.Base]/") {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      Crash(data);
      return false;
     } else {
      var Data = RenderView(data),
            AccessCode = Data["AccessCode"],
            Response = Data["Response"];
      if(AccessCode === "Denied") {
       Dialog(Response);
      } else {
       $("body > div").each(function() {
        if(!$(this).hasClass("Boot") && !$(this).hasClass("RegSel")) {
         $(this).remove();
        }
       });
       setTimeout(function() {
        $("body").append(Response);
        AddContent();
        Bulletins();
       }, 10);
      }
     }
    }
   },
   url: base + $.b64.d("[App.MainUI]")
  });
 }
 setInterval(function() {
  Language = LocalData("Get", "Language");
  if(Language === "" || typeof(Language) === "undefined") {
   $(".RegSel").fadeIn(500);
  }
 }, 15);
 setTimeout(function() {
  $(".Boot").fadeOut(500);
 }, 1000);
});