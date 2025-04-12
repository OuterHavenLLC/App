const DefaultContainer = ".Content",
          DefaultUI = "[App.DefaultUI]",
          DITkey = "[App.DITkey]";
          Inputs = "input, number, select, textarea",
          Language = "[App.Language]",
          Loading = "<img src='[Media:Loading]' style='margin:0em auto;width:1em'/>",
          UIVariant = "[App.DefaultUI]",
          base = "[App.Base]/?_API=Web&";
function AddContent() {
 const Daemon = () => {
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = JSON.parse(AESdecrypt(data));
     if(typeof Data.View !== "undefined") {
      if(!$(".AddContent").is(":visible")) {
       $("body").append(Data.View);
       setTimeout(() => {
        $(".AddContent").fadeIn(500);
       }, 500);
      }
     } else if($(".AddContent").is(":visible")) {
      $(".AddContent").fadeOut(500);
      setTimeout(() => {
       $(".AddContent").remove(); 
      }, 600);
     }
    }
   },
   url: base + AESdecrypt("[App.AddContent]")
  });
 }
 Daemon();
 setInterval(() => {
  Daemon();
 }, 6000);
}
function AESdecrypt(data = "") {
 if(!data || typeof data === "undefined") {
  return data;
 } else {
  try {
   var Key = CryptoJS.enc.Base64.parse(DITkey),
         decrypted = "",
         hashedKey = CryptoJS.SHA256(Key),
         KeyWordList = hashedKey;
   decrypted = CryptoJS.AES.decrypt(data, KeyWordList, {
    mode: CryptoJS.mode.ECB,
    padding: CryptoJS.pad.Pkcs7
   });
   return decrypted.toString(CryptoJS.enc.Utf8);
  } catch(error) {
   console.error("AES Decryption error:", error.message);
  }
 }
}
function AESencrypt(data = "") {
 if(!data || typeof data === "undefined") {
  return data;
 } else {
  try {
   var Key = CryptoJS.enc.Base64.parse(DITkey),
         DataWordList = CryptoJS.enc.Utf8.parse(data),
         encrypted = "",
         hashedKey = CryptoJS.SHA256(Key),
         KeyWordList = hashedKey;
   encrypted = CryptoJS.AES.encrypt(DataWordList, KeyWordList, {
    mode: CryptoJS.mode.ECB,
    padding: CryptoJS.pad.Pkcs7
   });
   return encrypted.toString();
  } catch(error) {
   console.error("AES Encryption error:", error.message);
  }
 }
}
function Base64decrypt(data) {
 if(!data) {
  return "";
 } else {
  try {
   data = CryptoJS.enc.Base64.parse(data).toString(CryptoJS.enc.Utf8);
   try {
    return JSON.parse(data);
   } catch {
    return data;
   }
  } catch(error) {
   console.error("Base 64 Decryption error:", error.message);
  }
 }
}
function Base64encrypt(data) {
 try {
  var data = typeof data === "string" ? data : JSON.stringify(data);
        data = CryptoJS.enc.Base64.stringify(CryptoJS.enc.Utf8.parse(data));
  return data;
 } catch(error) {
  console.error("Base 64 Encryption error:", error.message);
 }
}
function Bulletins() {
 var Daemon = () => {
  var Bulletins = ".Bulletins";
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     var Data = RenderView(data);
     if(Data.View > 0) {
      Bulletin = AESdecrypt("[App.Bulletin]");
      Bulletin = Bulletin.replaceAll("[Bulletin.Date]", "Just now");
      Bulletin = Bulletin.replaceAll("[Bulletin.From]", "[App.Name]");
      Bulletin = Bulletin.replaceAll("[Bulletin.ID]", "NewBulletins");
      Bulletin = Bulletin.replaceAll("[Bulletin.Message]", "You have " + Response + " new Bulletins!");
      Bulletin = Bulletin.replaceAll("[Bulletin.Options]", "<button class='CloseBulletins v2 v2w'>Okay</button>");
      Bulletin = Bulletin.replaceAll("[Bulletin.Picture]", "<img class='c2' src='[Media:LOGO]' style='width:100%'/>");
      $(Bulletins).html(Bulletin);
      setTimeout(() => {
       $(Bulletins).show("slide", {direction: "right"}, 500);
       setTimeout(() => {
        $(Bulletins).hide("slide", {direction: "right"}, 500);
        setTimeout(() => {
         $(Bulletins).empty();
        }, 5000);
       }, 10000);
      }, 500);
     }
    }
   },
   url: base + AESdecrypt("[App.Bulletins]")
  });
 };
 Daemon();
 setInterval(() => {
  Daemon();
 }, 120000);
}
function Card(data) {
 var Data = data || {},
       Action = Data.Action || "",
       Front = Data.Front || "",
       Card = "",
       ID = Data.ID || UUID();
 $(".CloseCard, .OpenCard").each(() => {
  this.disabled = true;
 });
 $("body").append("<div class='CardOverlay " + ID + " Overlay h'></div>");
 const FrontFace = (typeof Front === "object") ? ChangeData(Front) : Promise.resolve(Front);
 return FrontFace.then(response => {
  Card = "<div class='CardFront Frosted Rounded ShadowedLarge h scr'>\r\n";
  Card += "<div class='CardHeader'>\r\n";
  Card += "<div class='Desktop50'>\r\n";
  Card += "<button class='CardButton CloseCard' data-id='" + ID + "'>Close</button>\r\n";
  Card += "</div>\r\n";
  Card += "<div class='Desktop50 RightText'>\r\n";
  Card += Action + "\r\n";
  Card += "</div>\r\n";
  Card += "</div>\r\n";
  Card += "<div class='CardCC FixedHeight scr'>\r\n";
  Card += response + "\r\n";
  Card += "</div>\r\n";
  Card += "</div>";
  $("." + ID).html(Card);
  $("." + ID).fadeIn(500);
  $("." + ID).find(".CardFront").show("slide", {
   direction: "down"
  }, 500);
  setTimeout(() => {
   $(".CloseCard, .OpenCard").each(() => {
    this.disabled = false;
   });
   $("." + ID).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
  }, 600);
 });
}
function ChangeData(data) {
 var Data = data || {},
       Change = Data.ChangeData || {},
       View = "";
 if(typeof Data.Extension !== "undefined") {
  View = AESdecrypt(Data.Extension);
  const promises = Object.entries(Change).map(([key, value]) => {
   if($.isArray(value) || typeof value === "object") {
    return ChangeData(value).then(replacement => {
     View = View.replaceAll(key, replacement);
    });
   } else {
    View = View.replaceAll(key, PlainText({
     "BBCodes": 1,
     "Data": value
    }));
    return Promise.resolve();
   }
  });
  return Promise.all(promises).then(() => View);
 } else if(typeof Data.ExtensionID !== "undefined") {
  return LoadFromDatabase("Extensions", Data.ExtensionID).then(Extension => {
   if(!Extension || !Extension.Data) {
    Dialog({
     "Body": "Extension or Extension Data is undefined for <em>" + Data.ExtensionID + "</em>."
    });
    return "";
   } else {
    View = AESdecrypt(Extension.Data);
    const promises = Object.entries(Change).map(([key, value]) => {
     if($.isArray(value) || typeof value === "object") {
      return ChangeData(value).then(replacement => {
       View = View.replaceAll(key, replacement);
      });
     } else {
      View = View.replaceAll(key, PlainText({
       "BBCodes": 1,
       "Data": value
      }));
      return Promise.resolve();
     }
    });
    return Promise.all(promises).then(() => View);
   }
  }).catch(error => {
   Dialog({
    "Body": "Error retrieving extension.",
    "Scrollable": JSON.stringify(error)
   });
   return "";
  });
 } else {
  return Promise.resolve("");
 }
}
function CloseCard(ID = "") {
 var Overlay = "." + ID;
 if(ID === "" || typeof ID === "undefined") {
  Overlay = ".CardOverlay:last"
 }
 $(".CloseCard, .OpenCard").each(() => {
  this.disabled = true;
 });
 $(Overlay).find(".CardFront").hide("slide", {direction: "down"}, 500);
 $(Overlay).fadeOut(500);
 setTimeout(() => {
  $(".CloseCard, .OpenCard").each(() => {
   this.disabled = false;
  });
  $(Overlay).fadeOut(500);
  setTimeout(() => {
   $(Overlay).remove();
  }, 600);
 }, 600);
}
function CloseDialog(ID = "") {
 var Overlay = "." + ID;
 if(ID === "" || typeof ID === "undefined") {
  Overlay = ".DialogOverlay:last"
 }
 $(".CloseDialog, .OpenDialog").each(() => {
  this.disabled = true;
 });
 $(Overlay).fadeOut(500);
 $(Overlay + " .Dialog").hide("scale");
 setTimeout(() => {
  $(".CloseDialog, .OpenDialog").each(() => {
   this.disabled = false;
  });
  setTimeout(() => {
   $(Overlay).remove();
  }, 600);
 }, 600);
}
function CloseFirSTEPTool(ID = "") {
 var FST = ".FST" + ID;
 if(ID === "" || typeof ID === "undefined") {
  FST = ".FST:last"
 }
 $(".CloseFirSTEPTool, .OpenFirSTEPTool").each(() => {
  this.disabled = true;
 });
 $(FST).hide("slide", {direction: "right"}, 500);
 setTimeout(() => {
  $(".CloseFirSTEPTool, .OpenFirSTEPTool").each(() => {
   this.disabled = false;
  });
  $(FST).remove();
 }, 600);
}
function CloseNetMap() {
 if($(".NetMap").is(":visible")) {
  $(".CloseNetMap, .OpenNetMap").each(() => {
   this.disabled = true;
  });
  $(".NetMap .ToggleAnimation").slideDown(500);
  setTimeout(() => {
   $(".CloseNetMap, .OpenNetMap").each(() => {
    this.disabled = false;
   });
   $(".NetMap").fadeOut(500);
   setTimeout(() => {
    $(".NetMap").remove();
   }, 500);
  }, 500);
 }
}
function Crash(data = "") {
 Dialog({
  "Body": "An internal error has ocurred and the request could not be completed. Please refer to the console for more information on this error.",
  "Header": "Crash Report",
  "Scrollable": data
 });
}
function DeleteContainer(button) {
 var Button = button,
       Container = $(Button).closest($(Button).attr("data-target"));
 $(Container).slideUp(500);
 setTimeout(() => {
  $(Container).remove();
 }, 500);
}
function Dialog(data) {
 var Data = data || {},
       Actions = "",
       ActionsList = Data.Actions || "",
       Body = Data.Body || "",
       Dialog = "",
       Header = Data.Header || "Error",
       ID = Data.ID || UUID(),
       NoClose = Data.NoClose || 0,
       Scrollable = Data.Scrollable || "";
 $(".CloseDialog, .OpenDialog").each(() => {
  this.disabled = true;
 });
 $("body").append("<div class='DialogOverlay " + ID + " Overlay h'></div>");
 const FrontFace = (typeof Body === "object") ? ChangeData(Body) : Promise.resolve(Body);
 return FrontFace.then(response => {
  if(ActionsList !== "" && typeof ActionsList !== "undefined") {
   $(ActionsList).each(function(key, value) {
    Actions += value;
   });
  } if(NoClose === 0) {
   Actions += "<button class='CloseDialog v2 v2w' data-id='" + ID + "'>Cancel</button>\r\n";
  }
  Dialog = "<div class='Frosted Dialog Rounded Shadowed h scr'>\r\n";
  if(Header !== "" && typeof Header !== "undefined") {
   Dialog += "<h3 class='CenterText'>" + Header + "</h3>\r\n";
  } if(response !== "" && typeof response !== "undefined") {
   Dialog += "<p class='CenterText'>" + response + "</p>\r\n";
  } if(Scrollable !== "" && typeof Scrollable !== "undefined") {
   Dialog += "<div class='NONAME scr' style='max-height:400px'>\r\n";
   Dialog += Scrollable + "\r\n";
   Dialog += "</div>\r\n";
  }
  Dialog += Actions + "\r\n";
  Dialog += "</div>";
  $("." + ID).html(Dialog);
  $("." + ID).fadeIn(500);
  $("." + ID + " .Dialog").show("scale");
  setTimeout(() => {
   $(".CloseDialog, .OpenDialog").each(() => {
    this.disabled = false;
   });
   $("." + ID).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
  }, 500);
 });
}
function GoToParent(Data) {
 try {
  var Parent = $(".ParentPage" + Data).parent();
  $(Parent).find(".ViewPage" + Data).fadeOut(500);
  setTimeout(() => {
   $(Parent).find(".ParentPage" + Data).show("slide", {
    direction: "left"
   }, 500);
   setTimeout(() => {
    $(Parent).find(".ViewPage" + Data).remove();
   }, 500);
  }, 500);
 } catch(error) {
  Dialog({
   "Body": "An error ocurred while switching to the parent view.",
   "Scrollable": JSON.stringify(error)
  });
 }
}
function GoToView(Container, Container2, Data = "") {
 var Parent = $("." + Container).parent();
 $(Parent).append("<div class='" + Container2 + " h scr'></div>");
 $(Parent).find("." + Container).hide("slide", {
  direction: "left"
 }, 500);
 setTimeout(() => {
  if(Data !== "" && typeof Data !== "undefined") {
   $(Parent).find("." + Container2).html(Data);
  }
  $(Parent).find("." + Container2).fadeIn(500);
 }, 600);
}
function Encrypt(data) {
 if($.isArray(data) && typeof data !== "undefined") {
  $.each(data, (key, input) => {
   input.value = AESencrypt(input.value);
  });
  return data;
 } else {
  Dialog({
   "Body": "The encoder expects a populated list."
  });
 }
}
function ExecuteCommands(Commands = "", Executed = "No") {
 if(typeof Commands === "object" && Executed === "No") {
  $.each(Commands, (Key, Command) => {
   const AES = Command.AES || "No",
             Name = Command.Name || "",
             Parameters = Command.Parameters || {};
   let ParameterCount = 0;
   ParameterCount = Object.keys(Parameters).length;
   if(Name === "AddContent") {
    AddContent();
   } else if(Name === "Bulletins") {
    Bulletins();
   } else if(Name === "SignIn" && ParameterCount === 1) {
    SignIn(Command.Parameters[0]);
   } else if(Name === "UpdateContent" && ParameterCount === 2) {
    UpdateContent(Parameters[0], Parameters[1]);
   } else if(Name === "UpdateContentAES" && ParameterCount === 3) {
    UpdateContent(Parameters[0], Parameters[1], Parameters[2]);
   } else if(Name === "UpdateCoverPhoto" && ParameterCount === 2) {
    UpdateCoverPhoto(Parameters[0], Parameters[1]);
   } else if(Name === "UpdateContentRecursive" && ParameterCount === 3) {
    UpdateContentRecursive(Parameters[0], Parameters[1], Parameters[2]);
   } else if(Name === "UpdateContentRecursiveAES" && ParameterCount === 4) {
    UpdateContentRecursive(Parameters[0], Parameters[1], Parameters[2], Parameters[3]);
   }
  });
 }
}
function FST(data) {
 var Data = data || {},
       ID = Data.ID || UUID();
 $("body").append("<div class='Frosted FST FST" + ID + " RoundedLarge Shadowed h scr'></div>");
 $(".FST" + ID).html(Data);
 setTimeout(() => {
  $(".FST" + ID).show("slide", {
   direction: "right"
  }, 500);
 }, 600);
}
function InstantSignOut() {
 setTimeout(() => {
  LocalData("Purge", "SecurityKey");
  SetUIVariant(DefaultUI);
 }, 1000);
}
function LiveView(input) {
 var Daemon = () => {
  var Input = $(document).find(input),
        DLC = $(Input).val(),
        Preview = $(Input).attr("data-preview-destination");
  if($(Input).length && $(Preview).length) {
   if($(Preview).is(":visible")) {
    $.ajax({
     error: (error) => {
      Dialog({
       "Body": "Data retrieval error, please see below.",
       "Scrollable": JSON.stringify(error)
      });
     },
     headers: {
      Language: AESencrypt(LocalData("Get", "Language")),
      Token: AESencrypt(LocalData("Get", "SecurityKey"))
     },
     method: "POST",
     success: (data) => {
      if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
       Crash(data);
       return;
      } else {
       const Data = RenderView(data);
       if(Data.View !== "" && typeof Data.View === "undefined") {
        Data.View.then(response => {
         $(Preview).html(response);
         ExecuteCommands(Data.Commands);
        }).catch(error => {
         Dialog({
          "Body": "LiveView: Error rendering view data. Please see below for more information:",
          "Scrollable": JSON.stringify(error)
         });
        });
       }
      }
     },
     url: base + Base64decrypt($(Input).attr("data-live-view")) + DLC
    });
   }
  }
 };
 Daemon();
 setInterval(() => {
  Daemon();
 }, 15000);
}
function LocalData(action = "Get", identifier = "", data = {}) {
 if(action === "Get") {
  if(window.localStorage.getItem(identifier)) {
   if(identifier !== "" && typeof identifier !== "undefined") {
    data = window.localStorage.getItem(identifier) || "";
    data = JSON.parse(data);
   }
  } else {
   data = "";
  }
  return data;
 } else if(action === "Purge") {
  if(identifier === "" || typeof identifier === "undefined") {
   window.localStorage.clear();
  } else {
   window.localStorage.removeItem(identifier);
  }
 } else if(action === "Save") {
  if(data !== {} && identifier !== "" && typeof identifier !== "undefined") {
   data = JSON.stringify(data);
   window.localStorage.setItem(identifier, data);
  }
 }
}
function LoadFromDatabase(Store, ID) {
 if(typeof Store !== "string" || Store.trim() === "") {
  return Promise.reject(new Error("The Store Identifier is missing."));
 } if(ID === undefined || ID === null) {
  return Promise.reject(new Error("The Data Identifier is missing."));
 }
 return new Promise((resolve, reject) => {
  const openRequest = indexedDB.open("OuterHaven");
  openRequest.onupgradeneeded = () => {
   const db = openRequest.result;
   if(!db.objectStoreNames.contains(Store)) {
    db.createObjectStore(Store, { keyPath: "ID" });
   }
  };
  openRequest.onerror = () => reject(new Error("Failed to open database: " + openRequest.error));
  openRequest.onsuccess = () => {
   const db = openRequest.result;
   let transaction;
   try {
    transaction = db.transaction(Store, "readonly");
   } catch(error) {
    db.close();
    reject(new Error("Transaction failed for store " + Store + ": " + error.message + "."));
    return;
   }
   const store = transaction.objectStore(Store);
   const getRequest = store.get(ID);
   getRequest.onsuccess = () => {
    const result = getRequest.result;
    db.close();
    resolve(result ? result : undefined);
   };
   getRequest.onerror = () => {
    db.close();
    reject(new Error("Failed to retrieve ID " + ID + " from store " + Store + ": " + getRequest.error + "."));
   };
  };
 });
}
function OpenCard(View, Encryption = "") {
 if(Encryption === "AES") {
  View = AESdecrypt(View);
 } else {
  View = Base64decrypt(View);
 }
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    RenderView(data);
   }
  },
  url: base + View
 });
}
function OpenDialog(View, Encryption = "") {
 if(Encryption === "AES") {
  View = AESdecrypt(View);
 } else {
  View = Base64decrypt(View);
 }
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    RenderView(data);
   }
  },
  url: base + View
 });
}
function OpenFirSTEPTool(Ground, FirSTEPTool) {
 if(Ground !== "" && typeof Ground !== "undefined") {
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     if(Data.View !== "" && typeof Data.View === "undefined") {
      Data.View.then(response => {
       $(DefaultContainer).html(response);
       ExecuteCommands(Data.Commands);
      }).catch(error => {
       Dialog({
        "Body": "OpenFirSTEPTool: Error rendering view data. Please see below for more information:",
        "Scrollable": JSON.stringify(error)
       });
      });
     }
    }
   },
   url: base + AESdecrypt(Ground)
  });
 } if(FirSTEPTool !== "" && typeof FirSTEPTool !== "undefined") {
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     if(Data.View !== "" && typeof Data.View === "undefined") {
      Data.View.then(response => {
       FST(response);
       ExecuteCommands(Data.Commands);
      }).catch(error => {
       Dialog({
        "Body": "OpenFirSTEPTool: Error rendering view data. Please see below for more information:",
        "Scrollable": JSON.stringify(error)
       });
      });
     }
    }
   },
   url: base + AESdecrypt(FirSTEPTool)
  });
 }
}
function OpenNetMap(View, Encryption = "") {
 if(Encryption === "AES") {
  View = AESdecrypt(View);
 } else {
  View = Base64decrypt(View);
 }
 $("body").append("<div class='Frosted NetMap h scr'></div>");
 $(".CloseNetMap, .OpenNetMap").each(() => {
  this.disabled = true;
 });
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    const Data = RenderView(data);
    if(Data.View !== "" && typeof Data.View === "undefined") {
     Data.View.then(response => {
      $(".NetMap").html(response);
      setTimeout(() => {
       $(".CloseNetMap, .OpenNetMap").each(() => {
        this.disabled = false;
       });
       $(".NetMap").fadeIn(500);
       $(".NetMap .ToggleAnimation").slideUp(1000);
       ExecuteCommands(Data.Commands);
      }, 500);
     }).catch(error => {
      Dialog({
       "Body": "OpenNetMap: Error rendering view data. Please see below for more information:",
       "Scrollable": JSON.stringify(error)
      });
     });
    }
   }
  },
  url: base + View
 });
}
function RefreshCoverPhoto(container, data = {}, disabled = "false") {
 if(data !== {} && typeof data !== "undefined" && disabled === "false") {
  var CoverPhoto = container || ".CoverPhoto",
        Image,
        Index = 0,
        NewSlides = {},
        SlideCount = 0;
  $.each(data, (key, value) => {
   NewSlides[SlideCount] = value;
   SlideCount = Math.round(SlideCount + 1);
  });
  if(CoverPhoto !== ".CoverPhoto" && typeof CoverPhoto !== "undefined") {
   Image = NewSlides[Math.floor(Math.random() * SlideCount)];
   $(CoverPhoto).css({
    "background": "url('" + Image + "') no-repeat center center fixed",
    "background-size": "cover",
    "transition": "background 1s ease-in-out"
   });
   setTimeout(() => {
    setInterval(() => {
     Image = NewSlides[Math.floor(Math.random() * SlideCount)];
     $(CoverPhoto).css({
      "background": "url('" + Image + "') no-repeat center center fixed",
      "background-size": "cover"
     });
    }, 10000);
   }, 600);
  }
 }
}
function RenderDesignView(container) {
 var Container = container || {},
       DesignView = $($(Container).attr("data-in")).val();
 if($(Container).is(":visible")) {
  DesignView = Base64encrypt(encodeURIComponent(DesignView));
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     if(Data.View !== "" && typeof Data.View === "undefined") {
      Data.View.then(response => {
       $(Container).html(response);
       $(Container).find("button, input, select, textarea").each(() => {
        this.disabled = true;
       });
       ExecuteCommands(Data.Commands);
      }).catch(error => {
       Dialog({
        "Body": "RenderDesignView: Error rendering view data. Please see below for more information:",
        "Scrollable": JSON.stringify(error)
       });
      });
     }
    }
   },
   url: base + Base64decrypt($(Container).attr("data-u")) + DesignView
  });
 }
}
function RenderInputs(Container, Data) {
 var Container = Container || DefaultContainer,
       Data = Data || {};
 if(Container !== "" && Data !== {}) {
  $(Container).html("");
  $(Data).each(function(key, input) {
   var Attributes,
         Input = input || {},
         OptionGroup = Input["OptionGroup"] || {},
         OptionGroupLabel,
         Options = Input["Options"] || {},
         Type = Input["Type"] || "Text";
   Attributes = Input["Attributes"] || {};
   setTimeout(() => {
    if(Attributes !== {} && Type !== "") {
     var RenderInput = "",
           RenderInputAttributes = "",
           RenderOptionGroup = "";
     if(Type !== "Select") {
      $.each(Attributes, (attribute, value) => {
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
       $.each(OptionGroup, (option, text) => {
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
            TextValue = (TextType === "hidden") ? Input["Value"] : Base64decrypt(Input["Value"]);
      RenderInput = "<input" + RenderInputAttributes + " value='" + TextValue + "'/>\r\n";
     } else if(Type === "TextBox") {
      RenderInput = "<textarea " + RenderInputAttributes + ">" + Base64decrypt(Input["Value"]) + "</textarea>\r\n";
      if(Options["WYSIWYG"] === 1) {
       RenderInput = "<textarea " + RenderInputAttributes + " rows='40'>" + Base64decrypt(Input["Value"]) + "</textarea>\r\n";
       $.ajax({
        error: (error) => {
         Dialog({
          "Body": "Data retrieval error, please see below.",
          "Scrollable": JSON.stringify(error)
         });
        },
        headers: {
         Language: AESencrypt(LocalData("Get", "Language")),
         Token: AESencrypt(LocalData("Get", "SecurityKey"))
        },
        method: "POST",
        success: (data) => {
         if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
          Crash(data);
          return;
         } else {
          const WData = RenderView(data);
          ExecuteCommands(WData.Commands);
          WData.View.then(response => {
           WYSIWYG = response;
           WYSIWYG = WYSIWYG.replaceAll("[WYSIWYG.ID]", Attributes["id"]);
           WYSIWYG = WYSIWYG.replaceAll("[WYSIWYG.TextBox]", RenderInput);
           RenderInput = WYSIWYG;
          }).catch(error => {
           Dialog({
            "Body": "WYSIWYG: Error rendering view data. Please see below:",
            "Scrollable": JSON.stringify(error)
           });
          });
         }
        },
        url: base + AESdecrypt("[App.WYSIWYG]")
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
  }).promise().done(() => {
   setTimeout(() => {
    $(Container).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
   }, 500);
  });
 }
}
function RenderView(data) {
 let Data = JSON.parse(AESdecrypt(data)),
      AccessCode = Data.AccessCode || "Denied",
      AddTopMargin = Data.AddTopMargin || 1,
      Commands = Data.Commands || "",
      NewVariant = Data.SetUIVariant || "",
      ResponseType = Data.ResponseType || "Dialog",
      Success = Data.Success || "",
      Title = Data.Title || "[App.Name]",
      View = Data.View || "";
 $(document).prop("title", Title);
 SetUIVariant(NewVariant);
 if(Data.Card !== "" && typeof Data.Card !== "undefined") {
  Card(Data.Card);
  setTimeout(() => {
   ExecuteCommands(Commands);
  }, 500);
 } if(Data.Dialog !== "" && typeof Data.Dialog !== "undefined") {
  Dialog(Data.Dialog);
  setTimeout(() => {
   ExecuteCommands(Commands);
  }, 500);
 } if(typeof View === "object") {
  View = ChangeData(View);
 }
 return {
  "AccessCode": AccessCode,
  "AddTopMargin": AddTopMargin,
  "Commands": Commands,
  "ResponseType": ResponseType,
  "Success": Success,
  "View": View
 };
}
function PlainText(data) {
 let View = data.Data || "";
 if(data.BBCodes && data.BBCodes === 1) {
  View = View.replaceAll(/\[b\](.*?)\[\/b\]/gis, "<strong>$1</strong>");
  View = View.replaceAll(/\[d:.(.*?)\](.*?)\[\/d\]/gis, "<div class=\"$1\">$2</div>\r\n");
  View = View.replaceAll(/\[d:#(.*?)\](.*?)\[\/d\]/gis, "<div id=\"$1\">$2</div>\r\n");
  View = View.replaceAll(/\[i\](.*?)\[\/i\]/gis, "<em>$1</em>");
  View = View.replaceAll(/\[u\](.*?)\[\/u\]/gis, "<u>$1</u>");
  View = View.replaceAll(/\[(.*?)\[(.*?)\]:(.*?)\]/gis, "<$1 $2>$3</$1>");
  View = View.replaceAll(/\[IMG:s=(.*?)&w=(.*?)\]/gis, "<img src=\"$1\" style=\"width:$2\"/>");
  View = View.replaceAll(/\[P:(.*?)\]/gis, "<p>$1</p>");
 } if(data.Decode && data.Decode === 1) {
  View = AESdecode(View);
 } if(data.Encode && data.Encode === 1) {
  View = AESencode(View);
 }
 return View;
}
function RenderVisibilityFilter(Container, Data) {
 var Container = Container || DefaultContainer,
       Data = Data || {},
       Response = {};
 if(Data !== {}) {
  var Filter = Data.Filter || "Privacy",
      Name = Data.Name || "Privacy",
      OptionGroup = {},
      Title = Data.Title || "Content Visibility",
      Value = Data.Value || "";
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
  $.each(Data, (key, input) => {
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
function ReSearch(input) {
 var Bar = input || {},
       Container = $(Bar).parent().find(".SearchContainer") || {},
       GridColumns = $(Bar).attr("data-columns") || "1",
       List = $(Bar).attr("data-list") || "",
       Offset = 0,
       Processor,
       Query = $(Bar).val() || "";
 if(Bar === {} || typeof Bar === "undefined") {
  Dialog({
   "Body": "The Re:Search input is missing."
  });
 } else if(Container === {} || typeof Container === "undefined") {
  Dialog({
   "Body": "The Re:Search list container is missing."
  });
 } else if(List === "" || typeof List === "undefined") {
  Dialog({
   "Body": "The Re:Search list source is missing."
  });
 } else {
  Processor = base + Base64decrypt(List) + "&query=" + Base64encrypt($(Bar).val());
  $(Container).empty();
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     var Data = JSON.parse(AESdecrypt(data)),//UPDATE SEARCH FIRST
           AccessCode = Data.AccessCode,
           End,
           Extension,
           Grid = GridColumns,
           Response = Data.Response,
           SearchID = "SearchList" + UUID();
     $(Container).html(Base64decrypt(Response.NoResults));
     if(AccessCode === "Accepted") {
      Extension = Base64decrypt(Response.Extension);
      End = Response.End || 0;
      if(Grid === "2") {
       Grid = "Grid2";
      } else if(Grid === "3") {
       Grid = "Grid3";
      } else if(Grid === "4") {
       Grid = "Grid4";
      } else {
       Grid = "NONAME";
      }
      $(Container).html("<div class='" + Grid + " " + SearchID + "'></div>");
      var List = getSortedList(Response.List),
            ListItems = 0,
            check = ($.type(List) !== "undefined" && List !== {}) ? 1 : 0,
            check = ($.type(List) === "object" || check === 1) ? 1 : 0;
      Container = $(Container).find("." + SearchID);
      if(check === 1) {
       check = (Query !== "" && typeof Query !== "undefined") ? 1 : 0;
       for(var i in List) {
        var KeyCheck = ($.type(List[i][0]) !== "undefined") ? 1 : 0,
              ValueCheck = ($.type(List[i][1]) !== "undefined") ? 1 : 0;
        if(KeyCheck === 1 && ValueCheck === 1) {
         var Search = (check === 0) ? 1 : 0,
               Result = Extension,
               value = List[i][1] || {};
         if(value !== {} && $.type(value) !== "undefined") {
          for(var i in value) {
           Result = Result.replaceAll(value[i][0], Base64decrypt(value[i][1]));
          } if(Result.search(Query) > -1) {
           Search = Search + 1;
          } if(Result.toLowerCase().search(Query.toLowerCase()) > -1) {
           Search = Search + 1;
          } if(Search > 0) {
           ListItems = ListItems + 1;
           $(Container).append(Result);
          }
         }
        }
       } if(ListItems === 0) {
        $(Container).html(Base64decrypt(Response.NoResults));
       } else {
        setInterval(() => {
         if($(Container).is(":visible") && $(Container).length && End === 0) {
          $.ajax({
           error: (error) => {
            Dialog({
             "Body": "Data retrieval error, please see below.",
             "Scrollable": JSON.stringify(error)
            });
           },
           headers: {
            Language: AESencrypt(LocalData("Get", "Language")),
            Token: AESencrypt(LocalData("Get", "SecurityKey"))
           },
           method: "POST",
           success: (data) => {
            if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
             Crash(data);
             return;
            } else {
             var Data = JSON.parse(AESdecrypt(data)),//UPDATE SEARCH FIRST
                   AccessCode = Data.AccessCode,
                   Response = Data.Response;
             End = Response.End || 0;
             if(End === 0) {
              Offset += Response.Limit;
             }
             var List = getSortedList(Response.List),
                   ListItems = 0,
                   check = ($.type(List) !== "undefined" && List !== {}) ? 1 : 0,
                   check = ($.type(List) === "object" || check === 1) ? 1 : 0;
             if(check === 1) {
              check = (Query !== "" && typeof Query !== "undefined") ? 1 : 0;
              for(var i in List) {
               var KeyCheck = ($.type(List[i][0]) !== "undefined") ? 1 : 0,
                     ValueCheck = ($.type(List[i][1]) !== "undefined") ? 1 : 0;
               if(KeyCheck === 1 && ValueCheck === 1) {
                var Search = (check === 0) ? 1 : 0,
                      Result = Extension,
                      value = List[i][1] || {};
                if(value !== {} && $.type(value) !== "undefined") {
                 for(var i in value) {
                  Result = Result.replaceAll(value[i][0], Base64decrypt(value[i][1]));
                 } if(Result.search(Query) > -1) {
                  Search = Search + 1;
                 } if(Result.toLowerCase().search(Query.toLowerCase()) > -1) {
                  Search = Search + 1;
                 } if(Search > 0) {
                  ListItems = ListItems + 1;
                  $(Container).append(Result);
                 }
                }
               }
              }
             }
            }
           },
           url: Processor + "&Offset=" + Offset
          });
         }
        }, 4000);
       }
      }
     }
    }
   },
   url: Processor
  });
 }
}
function SaveToDatabase(Store, Data) {
 if(typeof Store !== "string" || Store.trim() === "") {
  return Promise.reject(new Error("The Store Identifier is missing."));
 } if(!Array.isArray(Data)) {
  return Promise.reject(new Error("Data must be an array."));
 } for(const pair of Data) {
  if(!pair.hasOwnProperty("ID")) {
   return Promise.reject(new Error("Each data item must have an ID property."));
  }
 }
 return new Promise((resolve, reject) => {
  const openRequest = indexedDB.open("OuterHaven");
  openRequest.onupgradeneeded = (event) => {
   const db = event.target.result;
   if(db.objectStoreNames.contains(Store)) {
    db.deleteObjectStore(Store);
   }
   db.createObjectStore(Store, {keyPath: "ID"});
  };
  openRequest.onerror = () => reject(openRequest.error);
  openRequest.onsuccess = () => {
   const db = openRequest.result;
   const transaction = db.transaction(Store, "readwrite");
   const store = transaction.objectStore(Store, {keyPath: "ID"});
   Data.forEach(pair => {
    store.put(pair);
   });
   transaction.oncomplete = () => {
    db.close();
    resolve();
   };
   transaction.onerror = () => {
    db.close();
    reject(transaction.error);
   };
  };
  openRequest.onerror = () => reject(openRequest.error);
 });
}
function SetUIVariant(NewVariant = DefaultUI) {
 if($(location).attr("href") === "[App.Base]/" && NewVariant !== UIVariant) {
  UIVariant = NewVariant;
  $(".SideBar").hide("slide", {direction: "left"}, 500);
  $(".TopBar .MenuContainer").hide("slide", {direction: "up"}, 500);
  setTimeout(() => {
   if(NewVariant === "0") {
    $(".TopHome, .TopSearchBar").hide("slide", {direction: "up"}, 500);
    $(".TopBar").hide("slide", {direction: "up"}, 500);
    $(".TopBar .MenuContainer").hide("slide", {direction: "up"}, 500);
    $(".TopBarClassic").show("slide", {direction: "up"}, 500);
   } else if(NewVariant === "1") {
    $(".SideBar").hide("slide", {direction: "left"}, 500);
    $(".TopHome, .TopSearchBar").hide("slide", {direction: "up"}, 500);
    $(".TopBarClassic").hide("slide", {direction: "up"}, 500);
    $(".TopBar").show("slide", {direction: "down"}, 500);
   } else if(NewVariant === "2") {
    $(".TopHome, .TopSearchBar").show("slide", {direction: "up"}, 500);
    $(".TopBar .MenuContainer").hide("slide", {direction: "up"}, 500);
    $(".TopBar").hide("slide", {direction: "up"}, 500);
    $(".TopBarClassic").hide("slide", {direction: "up"}, 500);
   }
  }, 600);
 }
}
function SignIn(SecurityKey = "") {
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    const Data = JSON.parse(AESdecrypt(data));
    ExecuteCommands(Data.Commands);
    LocalData("Save", "SecurityKey", SecurityKey);
    GoToParent("MainView");
   }
  },
  url: base + AESdecrypt("[App.MainUI]")
 });
}
function SignOut() {
 InstantSignOut();
 setTimeout(() => {
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     ExecuteCommands(Data.Commands);
     Data.View.then(response => {
      $(DefaultContainer).html(response);
      UpdateContent(".Menu", "[App.Menu]", "AES");
      $(".SideBar").hide("slide", {direction: "left"}, 500);
      $(".TopBar .MenuContainer").slideUp(500);
     }).catch(error => {
      Dialog({
       "Body": "SignOut: Error rendering view data. Please see below:",
       "Scrollable": JSON.stringify(error)
      });
     });
    }
   },
   url: base + AESdecrypt("[App.Gateway]")
  });
 }, 600);
}
function UpdateButton(button, data) {
 var Attributes,
       Button = button || {},
       Data = data || {},
       Text = $(button).text() || "Error";
 if(Button !== {} && Data !== {}) {
  Attributes = Data.Attributes || {};
  Text = Data.Text || Text;
  if(Attributes !== {}) {
   $.each(Attributes, (key, value) => {
    $(Button).attr(key, value);
   });
  }
  $(Button).html(Text);
 }
}
function UpdateContent(Container, View, Encryption = "") {
 Container = Container || DefaultContainer;
 if(Encryption === "AES") {
  View = AESdecrypt(View);
 } else {
  View = Base64decrypt(View);
 } if($(Container).html() === "") {
  $(Container).html("<h4 class='CenterText InnerMargin'>" + Loading + "</h4>\r\n");
 }
 setTimeout(() => {
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     Data.View.then(response => {
      $(Container).empty();
      if(Data.AddTopMargin === 1) {
       $(Container).append("<div class='TopBarMargin'></div>\r\n");
      }
      $(Container).append(response);
      ExecuteCommands(Data.Commands);
     }).catch(error => {
      Dialog({
       "Body": "UpdateContent: Error rendering view data. Please see below:",
       "Scrollable": JSON.stringify(error)
      });
     });
    }
   },
   url: base + View
  });
 }, 600);
}
function UpdateContentRecursive(Container, View, Interval = 6000, Encryption = "") {
 UpdateContent(Container, View, Encryption);
 setInterval(() => {
  UpdateContent(Container, View, Encryption);
 }, Interval);
}
function UpdateCoverPhoto(Container, Photo) {
 var Container = Container || DefaultContainer,
       Photo = Photo || "";
 if(Photo !== "" && typeof Photo !== "undefined") {
  $(Container).css({
   "background": "url('" + Photo + "') no-repeat center center fixed",
   "backgroundSize": "cover"
  });
 }
}
function UpdateUIVariant(NewUIVariant) {
 var NewUIVariant = NewUIVariant || UIVariant;
 SetUIVariant(NewUIVariant);
 $(".PersonalUIVariant").val(NewUIVariant);
}
function Upload(Button) {
 var Form = $(Button).attr("data-form"),
       Inputs = "input, number, select, textarea",
       Pass = 1,
       Processor = $(Button).attr("data-processor"),
       Text = $(Button).text();
 $(Button).text(Text);
 $(Button).prop("disabled", true);
 $(Form).find(".req").each(() => {
  $(this).removeClass("Red");
  if($(this).val() === "") {
   $(this).addClass("Red");
   Pass = 0;
  }
 });
 if(Pass === 0) {
  $(Button).text(Text);
  $(Button).prop("disabled", false);
  return;
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
   }
  } for(var i = 0; i < Inputs.length; i++) {
   Data.append(Inputs[i].name, AESencrypt(Inputs[i].value));
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
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    var AccessCode = "Denied",
          Class,
          Passed,
          Response,
          Type,
          Data = JSON.parse(AESdecrypt(data));
    AccessCode = Data.AccessCode || AccessCode;
    Response = Data.JSON || {};
    Type = Data.ResponseType || "Dialog";
    if(Response === "" || typeof Response === "undefined") {
     Dialog({
      "Body": "<em>[App.Name]</em> returned an empty response. Check the processor within the following URI fragment: " + Processor + "."
     });
    } else {
     if(AccessCode === "Denied") {
      Dialog({
       "Body": "The upload was rejected, please refer to the console.",
       "Scrollable": JSON.stringify(Response)
      });
     } else {
      Passed = Response["Passed"] || {};
      if(Passed !== {} && typeof Passed !== "undefined") {
       $($(Form).find(".EmptyOnSuccess")).each(function(k, v) {
        $(this).val("");
       });
       $(".Uploads").html("").addClass("SideScroll");
       for(var i = 0; i < Passed.length; i++) {
        $(".Uploads").append(Passed[i]["HTML"]);
       }
      }
     }
     $(Button).prop("disabled", false);
     $(Button).text("Upload");
    }
   }
  }, false);
  Request.open("POST", base + Base64decrypt(Processor), true);
  Request.setRequestHeader("Language", Base64encrypt(LocalData("Get", "Language")));
  Request.setRequestHeader("Token", Base64encrypt(LocalData("Get", "SecurityKey")));
  Request.send(Data);
 }
}
function UUID() {
 var UUID = Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
       UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
       UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
       UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
       UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
       UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
       UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
       UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
       UUID += uniqid();
 return UUID;
}
function W(link, target) {
 var W = window.open(link, target);
 W.focus();
}
function getCreditExchange(data) {
 setInterval(() => {
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
  return;
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
 setInterval(() => {
  if($(Range).is(":visible")) {
   $(Range).next().closest(".GetRangeValue").text($(Range).val());
  }
 }, 250);
}
function getSortedList(data) {
 var Response = [];
 $.each(data, (key, value) => {
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
$(document).on("change", "select[name='ProductQuantity']", (event) => {
 const $Input = $(event.currentTarget),
           Price = $Input.parent().find(".AddToCart").text(),
           Quantity = $Input.find("option:selected").val();
 Price = Price.replace("$", "");
 Price = parseInt(Price) * parseInt(Quantity);
 $Input.parent().find(".AddToCart").text("$" + Price);
});
$(document).on("click", ".Attach", (event) => {
 const $Button = $(event.currentTarget),
           $Input = $(document).find(Base64decrypt($Button.attr("data-input"))),
           Media = $Button.attr("data-media") || "";
 $Button.prop("disabled", true);
 if(!Input.length) {
  Dialog({
   "Body": "Failed to find the attachment input. Here is the source data: " + $Button.attr("data-input") + "."
  });
 } else if(!Media.length) {
  Dialog({
   "Body": "Failed to find the attachment media. Here is the source data: " + Media + "."
  });
 } else {
  $Input.val(Media);
  $Button.text(Loading);
  CloseCard();
 }
});
$(document).on("click", ".Clone", (event) => {
 const $Button = $(event.currentTarget),
           CloneID = "Clone" + UUID(),
           Destination = $Button.attr("data-destination"),
           Source = $($Button.attr("data-source")).text();
 Source = Base64decrypt(Source.trim());
 Source = Source.replaceAll("[Clone.ID]", CloneID);
 $(Destination).append(Source);
});
$(document).on("click", ".CloneAttachments", (event) => {
 const $Button = $(event.currentTarget),
           CloneID = "AttachmentClone" + UUID(),
           Destination = $Button.attr("data-destination"),
           InjectCloneID = (cloneID, data) => {
            const link = "",
                      pairs = data.substring(data.indexOf("?") + 1).split("&");
            for(var i = 0; i < pairs.length; i++) {
             if(!pairs[i]) {
              continue;
             } else {
              const pair = pairs[i].split("=");
              if(pair[0] === "AddTo") {
               pair[1] = Base64decrypt(pair[1]);
               pair[1] = pair[1].replaceAll("[Clone.ID]", cloneID);
               pair[1] = Base64encrypt(pair[1]);
              }
              link += "&" + pair[0] + "=" + pair[1];
             }
            }
            return link;
           },
           RemoveAfterUse = $Button.attr("data-remove") || "off",
           Source = $($Button.attr("data-source")).text();
 Source = Base64decrypt(Source.trim());
 Source = Source.replaceAll("[Clone.ID]", CloneID);
 AttachmentList = $(Source).find(".AttachmentList" + CloneID).attr("data-view");
 AttachmentList = Base64decrypt(AttachmentList);
 AttachmentList = AttachmentList.replaceAll("[Clone.ID]", CloneID);
 AttachmentList = InjectCloneID(CloneID, AttachmentList);
 $(Destination).append(Source);
 $(".AttachmentList" + CloneID).attr("data-view", Base64encrypt(AttachmentList));
 if(RemoveAfterUse === "on") {
  $Button.prop("disabled", true);
  $Button.slideUp(500);
  setTimeout(() => {
   $Button.remove();
  }, 600);
 }
});
$(document).on("click", ".CloseAllCards", () => {
 $(".CardOverlay").each(() => {
  setTimeout(() => {
   CloseCard();
  }, 500);
 });
});
$(document).on("click", ".CloseAllDialogs", () => {
 $(".DialogOverlay").each(() => {
  setTimeout(() => {
   CloseDialog();
  }, 500);
 });
});
$(document).on("click", ".CloseAllFirSTEPTools", () => {
 CloseAllFirSTEPTools();
});
$(document).on("click", ".CloseBottomBar", () => {
 $(".BottomBar").hide("slide", {direction: "down"}, 500);
 setTimeout(() => {
  $(".BottomBar").remove();
 }, 500);
});
$(document).on("click", ".CloseBulletins", () => {
 $(".Bulletins").hide("slide", {direction: "right"}, 500);
 setTimeout(() => {
  $(".Bulletins").empty();
 }, 600);
});
$(document).on("click", ".CloseCard", (event) => {
 CloseCard($(event.currentTarget).attr("data-id"));
});
$(document).on("click", ".CloseDialog", (event) => {
 CloseDialog($(event.currentTarget).attr("data-id"));
});
$(document).on("click", ".CloseFirSTEPTool", (event) => {
 CloseFirSTEPTool($(event.currentTarget).attr("data-id"));
});
$(document).on("click", ".CloseNetMap", () => {
 CloseNetMap();
});
$(document).on("click", ".CreditExchange", (event) => {
 var $Button = $(event.currentTarget),
       F = ".CE" + $Button.attr("data-id"),
       P = $Button.attr("data-p");
 P = $(F).find(".RI" + $Button.attr("data-id")).val();
 if($.isNumeric(P)) {
  $Button.prop("disabled", "true");
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     if(Data.AccessCode === "Denied") {
      $Button.prop("disabled", false);
     }
    }
   },
   url: base + Base64decrypt($Button.attr("data-u")) + Base64encrypt(P)
  });
 }
});
$(document).on("click", ".Delete", (event) => {
 const $Button = $(event.currentTarget),
           Processor = $Button.attr("data-processor"),
           Text = $Button.text();
 $Button.prop("disabled", true);
 $Button.text("Dispatching...");
 if(Processor === "" || typeof Processor === "undefined") {
  DeleteContainer($Button);
 } else {
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     if(Data.AccessCode === "Denied") {
      $Button.text("Try Later");
     } else {
      $Button.text("Done!");
      DeleteContainer($Button);
     }
    }
   },
   url: base + Base64decrypt(Processor)
  });
 }
 setTimeout(() => {
  $Button.prop("disabled", false);
  $Button.text(Text);
 }, 6000);
});
$(document).on("click", ".Disable", (event) => {
 $(event.currentTarget).prop("disabled", true);
});
$(document).on("click", ".Download", (event) => {
 const $Button = $(event.currentTarget),
           Downloader = $Button.attr("data-view") || "",
           Media = $Button.attr("data-media") || "";
 if(Media === "" || typeof Media === "undefined") {
  Dialog({
   "Body": "No media to download."
  });
 } else {
  Media = Base64decrypt(Media).split(";");
  $.each(Media, (key, value) => {
   $.ajax({
    data: {
     FilePath: value
    },
    error: (error) => {
     Dialog({
      "Body": "Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: AESencrypt(LocalData("Get", "Language")),
     Token: AESencrypt(LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: (blob, status, xhr) => {
     const Disposition = xhr.getResponseHeader("Content-Disposition"),
               File = "";
     if(Disposition && Disposition.indexOf("attachment") !== -1) {
      const FileRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                Matches = FileRegex.exec(Disposition);
      if(Matches != null && Matches[1]) {
       File = Matches[1].replace(/['"]/g, "");
       if(typeof window.navigator.msSaveBlob !== "undefined") {
        window.navigator.msSaveBlob(blob, File);
       } else {
        let URL = window.webkitURL || window.URL,
             DownloadURL;
        DownloadURL = URL.createObjectURL(blob);
        if(File) {
         const a = document.createElement("a");
         if(typeof a.download === "undefined") {
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
        setTimeout(() => {
         URL.revokeObjectURL(DownloadURL);
        }, 100);
       }
      }
     }
    },
    url: base + Base64decrypt(Downloader),
    xhrFields: {
     responseType: "blob"
    }
   });
  });
 }
});
$(document).on("click", ".GoToParent", (event) => {
 const $Button = $(event.currentTarget),
           Data = $Button.attr("data-type");
 GoToParent(Data);
});
$(document).on("click", ".GoToView", (event) => {
 var $Button = $(event.currentTarget),
       Data = $Button.attr("data-type").split(";"),
       Encryption = $Button.attr("data-encryption") || "",
       ID = Data[0],
       Parent = $(".ParentPage" + ID).parent(),
       View = Data[1];
 if(Encryption === "AES") {
  View = AESdecrypt(View);
 } else {
  View = Base64decrypt(View);
 }
 setTimeout(() => {
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     Data.View.then(response => {
      GoToView("ParentPage" + ID, "ViewPage" + ID, response);
      ExecuteCommands(Data.Commands);
     }).catch(error => {
      Dialog({
       "Body": "GoToView: Error rendering view data. Please see below:",
       "Scrollable": JSON.stringify(error)
      });
     });
    }
   },
   url: base + View
  });
 }, 500);
});
$(document).on("click", ".InstantSignOut", (event) => {
 InstantSignOut();
});
$(document).on("click", ".MarkAsRead", (event) => {
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   }
  },
  url: base + Base64decrypt($(event.currentTarget).attr("data-MAR"))
 });
});
$(document).on("click", ".Menu button", (event) => {
 $(".SideBar").hide("slide", {direction: "left"}, 500);
 $(".TopBar .MenuContainer").slideUp(500);
});
$(document).on("click", ".OpenBottomBar", (event) => {
 const $Button = $(event.currentTarget),
           View = $Button.attr("data-view") || "";
 if(View !== "" && typeof View !== "undefined") {
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     if(Data.View !== "" && typeof Data.View === "undefined") {
      Data.View.then(response => {
       $("body").append(response);
       $(".BottomBar").show("slide", {direction: "down"}, 500);
       ExecuteCommands(Data.Commands);
      }).catch(error => {
       Dialog({
        "Body": "OpenBottomBar: Error rendering view data. Please see below:",
        "Scrollable": JSON.stringify(error)
       });
      });
     }
    }
   },
   url: base + Base64decrypt(View)
  });
 }
});
$(document).on("click", ".OpenCard", (event) => {
 const $Button = $(event.currentTarget),
           View = $Button.attr("data-view");
 OpenCard(View);
});
$(document).on("click", ".OpenCardFromJSON", (event) => {
 const $Button = $(event.currentTarget),
           Data = $Button.attr("data-json") || Base64encrypt({});
 Card(Base64decrypt(data));
});
$(document).on("click", ".OpenDialog", (event) => {
 const $Button = $(event.currentTarget),
           Encryption = $Button.attr("data-encryption") || "",
           View = $Button.attr("data-view") || "";
 OpenDialog(View, Encryption);
});
$(document).on("click", ".OpenFirSTEPTool", (event) => {
 const $Button = $(event.currentTarget),
           FST = $Button.attr("data-fst"),
           Ground = $Button.attr("data-ground");
 OpenFirSTEPTool(Ground, FST);
});
$(document).on("click", ".PS", (event) => {
 const $Button = $(event.currentTarget),
            Data = $Button.attr("data-type").split(";");
 $.each($(Data[0]).find(Data[1]), function() {
  $(this).hide("slide", {direction: "left"}, 500);
 });
 setTimeout(() => {
  $(Data[2]).fadeIn(500);
 }, 600);
});
$(document).on("click", ".PSAccordion", (event) => {
 const $Button = $(event.currentTarget),
           Data = $Button.attr("data-type").split(";");
 $.each($(Data[0]).find(Data[1]), function() {
  $(this).slideUp(500);
 });
 setTimeout(() => {
  $(Data[2]).slideDown(500);
 }, 600);
});
$(document).on("click", ".PSBack", (event) => {
 const $Button = $(event.currentTarget),
           Data = $Button.attr("data-type").split(";");
 $.each($(Data[0]).find(Data[1]), function() {
  $(this).fadeOut(500);
 });
 setTimeout(() => {
  $(Data[2]).show("slide", {direction: "left"}, 500);
 }, 600);
});
$(document).on("click", ".PSPill", (event) => {
 const $Button = $(event.currentTarget),
            Data = $Button.attr("data-type").split(";");
 $($Button.parent(".Pill")).children("button").removeClass("Active");
 $Button.addClass("Active");
 $.each($(Data[0]).find(Data[1]), (event) => {
  $($(event.currentTarget)).fadeOut(500);
 });
 setTimeout(() => {
  $(Data[2]).show("slide", {direction: "right"}, 500);
 }, 600);
});
$(document).on("click", ".Reg", (event) => {
 const $Button = $(event.currentTarget),
           Language = $Button.attr("data-type") || "[App.Language]";
 LocalData("Save", "Language", Language);
 $(".RegSel").fadeOut(500);
});
$(document).on("click", ".RemoveFromAttachments", (event) => {
 const $Button = $(event.currentTarget),
           Input = $(document).find($Button.attr("data-input")),
           ID = $Button.attr("data-id"),
           Value = Base64decrypt($(Input).val());
 if(Value.search(";") > 0) {
  Value = Value.replace(ID + ";", "");
 } else {
  Value = Value.replace(ID, "");
 }
 if(Value === "" || typeof Value === "undefined") {
  $(Input).val(Value);
 } else {
  $(Input).val(Base64encrypt(Value));
 }
});
$(document).on("click", ".ReportContent", (event) => {
 const $Button = $(event.currentTarget),
           ID = $Button.attr("data-id"),
           Processor = Base64decrypt($Button.attr("data-processor")),
           Type = Base64encrypt($Button.attr("data-type"));
 $Button.prop("disabled", true);
 if(ID !== "" && typeof ID !== "undefined") {
  Processor = Processor.replace("[ID]", ID);
  $.ajax({
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     const Data = RenderView(data);
     if(Data.AccessCode === "Accepted") {
      CloseCard();
     }
    }
   },
   url: base + Processor + "&Type=" + Type
  });
 }
});
$(document).on("click", ".SendData", (event) => {
 var $Button = $(event.currentTarget),
       Form = $Button.attr("data-form"),
       FormData = Encrypt($(Form).find(Inputs).serializeArray()) || {},
       Pass = 0,
       Processor = Base64decrypt($Button.attr("data-processor")),
       RequiredInputs = $(Form).find(".req").length,
       Target = $Button.attr("data-target") || Form,
       Text = $Button.text();
 $Button.prop("disabled", true);
 $Button.text("&bull; &bull; &bull;");
 $.each($(Form).find("input[type='email']"), function() {
  $(this).removeClass("Red");
  if(!getEmailValidation($(this).val())) {
   $(this).addClass("Red");
   Dialog({
    "Body": "The email address format is invalid."
   });
  }
 });
 $.each($(Form).find(".req"), function() {
  $(this).removeClass("Red");
  if($(this).val() === "") {
   $(this).addClass("Red");
   Pass = 0;
  } else {
   Pass = 1;
  }
 });
 if(RequiredInputs === 0) {
  Pass = 1;
 } if(Pass === 0) {
  $Button.text(Text);
  $Button.prop("disabled", false);
  return;
 } else {
  $.ajax({
   data: FormData,
   error: (error) => {
    Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: AESencrypt(LocalData("Get", "Language")),
    Token: AESencrypt(LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     Crash(data);
     return;
    } else {
     var Class,
           Data = RenderView(data),
           AccessCode = Data.AccessCode,
           Processor = Base64decrypt(Processor),
           Success = Data.Success,
           Type = Data.ResponseType;
     if(!Data.Card && !Data.Dialog && typeof Data.View === "undefined") {
      Dialog({
       "Body": "<em>[App.Name]</em> returned an empty response. Check the following view: " + Processor + "."
      });
     } else if(AccessCode === "Accepted") {
      $.each($(Form).find(".EmptyOnSuccess"), function() {
       $(this).val("");
      });
      $.each($(Form).find(".RestoreDefaultValue"), function() {
       $(this).val($(this).attr("data-default"));
      });
      if(Success === "CloseCard") {
       CloseCard();
      } else if(Success === "CloseDialog") {
       CloseDialog();
      } if(Text === "Post") {
       $Button.text("Update");
      }
      setTimeout(() => {
       if(Type === "Destruct") {
        $(Target).toggle(500);
        setTimeout(() => {
         $(Target).remove();
        }, 600);
       } else if(Type === "GoToView") {
        const ViewPairID = Form.replace(".ParentPage", ""),
                  Parent = $(".ParentPage" + ViewPairID).parent();
        $(Parent).append("<div class='ViewPage" + ViewPairID + " h scr'></div>");
        $(Parent).find(".ParentPage" + ViewPairID).fadeOut(500);
        setTimeout(() => {
         if(Data.View !== "" && typeof Data.View !== "undefined") {
          Data.View.then(response => {
           $(Parent).find(".ViewPage" + ViewPairID).html(response).show("slide", {
            direction: "right"
           }, 500);
          }).catch(error => {
           Dialog({
            "Body": "SendData: Error rendering view data. Please see below for more information:",
            "Scrollable": JSON.stringify(error)
           });
          });
         }
        }, 600);
       } else if(Type === "ReplaceContent") {
        if(Data.View !== "" && typeof Data.View !== "undefined") {
         Data.View.then(response => {
          $(Target).html(response);
         }).catch(error => {
          Dialog({
           "Body": "SendData: Error rendering view data. Please see below for more information:",
           "Scrollable": JSON.stringify(error)
          });
         });
        }
       } else if(Type === "UpdateButton") {
        UpdateButton(Button, Data.View);
       } else if(Type === "UpdateText") {
        $Button.text(View);
       }
      }, 750);
      ExecuteCommands(Data.Commands);
     }
    } if(Type !== "UpdateButton") {
     $Button.text(Text);
    }
    $Button.prop("disabled", false);
   },
   url: base + Processor
  });
 }
});
$(document).on("click", ".SignOut", (event) => {
 $(event.currentTarget).text("Signing out...");
 SignOut();
});
$(document).on("click", ".ToggleElement", (event) => {
 const $Button = $(event.currentTarget),
           Delete = $Button.attr("data-delete") || "off",
           Elements = $Button.attr("data-elements").split(";") || {},
           Time = 500;
           Toggle = $Button.attr("data-toggle") || "on";
 $.each(Elements, (Key, Element) => {
  Time += 500;
  setTimeout(() => {
   if(Toggle === "on") {
    $(Element).slideDown(500);
   } else if(Toggle === "switch") {
    $(Element).toggle("slide", {direction: "up"}, 500);
   } else {
    $(Element).slideUp(500);
   }
  }, Time);
 });
 if(Delete === "on" && Toggle !== "switch") {
  $Button.slideUp(500);
  setTimeout(() => {
   $Button.remove();
  }, 600);
 }
});
$(document).on("click", ".ToggleMenu", (event) => {
 const Content = DefaultContainer
           Menu = ".TopBar .MenuContainer";
 if($(".FST").is(":visible")) {
  CloseFirSTEPTool();
 } else {
  if($(".NetMap").is(":visible")) {
   CloseNetMap();
  } else {
   if($(Menu).is(":visible")) {
    $(Menu).slideUp(500);
   } else {
    $(Menu).slideDown(500);
   }
  }
 }
});
$(document).on("click", ".ToggleNetMap", (event) => {
 const $Button = $(event.currentTarget);
 if($(".FST").is(":visible")) {
  CloseFirSTEPTool();
 } else {
  if($(".NetMap").is(":visible")) {
   CloseNetMap();
  } else {
   OpenNetMap($Button.attr("data-map"));
  }
 }
});
$(document).on("click", ".ToggleSideBar", (event) => {
 if($(".FST").is(":visible")) {
  CloseFirSTEPTool();
 } else {
  if($(".NetMap").is(":visible")) {
   CloseNetMap();
  } else {
   $(".SideBar").toggle("slide", {direction: "left"}, 500);
  }
 }
});
$(document).on("click", ".UpdateButton", (event) => {
 const $Button = $(event.currentTarget),
           Encryption = $Button.attr("data-encryption") || "",
           View = $Button.attr("data-processor") || "";
 if(Encryption === "AES") {
  View = AESdecrypt(View);
 } else {
  View = Base64decrypt(View);
 }
 $Button.prop("disabled", true);
 $Button.text(Loading);
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    const Data = RenderView(data);
    if(Data.View !== "" && typeof Data.View === "undefined") {
     Data.View.then(response => {
      UpdateButton($Button, response);
      $Button.prop("disabled", false);
      ExecuteCommands(Data.Commands);
     }).catch(error => {
      Dialog({
       "Body": "UpdateButton: Error rendering view data. Please see below:",
       "Scrollable": JSON.stringify(error)
      });
     });
    }
   }
  },
  url: base + View
 });
});
$(document).on("click", ".UpdateContent", (event) => {
 const $Button = $(event.currentTarget),
           Container = $Button.attr("data-container") || DefaultContainer,
           Encryption = $Button.attr("data-encryption") || "",
           View = $Button.attr("data-view") || "";
 $Button.prop("disabled", true);
 setTimeout(() => {
  UpdateContent(Container, View, Encryption);
 }, 500);
});
$(document).on("click", ".UploadFiles", (event) => {
 const $Button = $(event.currentTarget);
 event.preventDefault();
 $Button.prop("disabled", true);
 $Button.text(Loading);
 Upload($Button);
});
$(document).on("keyup", ".CheckIfNumeric", (event) => {
 const $Input = $(event.currentTarget),
           AllowsSymbols = $Input.attr("data-symbols") || 0,
           Pass = 0,
           Value = $Input.val();
 if(AllowsSymbols === "Y") {
  if(Value !== "" && typeof Value !== "undefined") {
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
 $Input.val(Value);
});
$(document).on("keyup", ".DiscountCodes", (event) => {
 const $Input = $(event.currentTarget),
           Data = Base64decrypt($Input.attr("data-u")),
           F = $Input.closest(".DiscountCodesCC");
 Data = Data.replace("[DC]", Base64encrypt($Input.val()));
 Data = Data.replace("[ID]", Base64encrypt($Input.attr("data-id")));
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    var data = Base64decrypt(data);
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
$(document).on("keyup", ".LinkData", (event) => {
 const $Input = $(event.currentTarget),
           Link = Base64encrypt($Input.val()),
           Preview = Base64decrypt($Input.attr("data-preview"));
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    const Data = RenderView(data);
    if(Data.View !== "" && typeof Data.View === "undefined") {
     Data.View.then(response => {
      $(".AddLink > .LinkPreview").html(response);
      ExecuteCommands(Data.Commands);
     }).catch(error => {
      Dialog({
       "Body": "LinkData: Error rendering view data. Please see below:",
       "Scrollable": JSON.stringify(error)
      });
     });
    }
   }
  },
  url: base + Preview + "&Link=" + Link
 });
});
$(document).on("keyup", ".ReSearch", (event) => {
 const $Input = $(event.currentTarget);
 ReSearch($Input);
});
$(document).on("keyup", ".SearchBar", (event) => {
 const $Input = $(event.currentTarget);
 if(getFSTvisibility() === "Accepted") {
  CloseNetMap();
  UpdateContent(DefaultContainer, Base64decrypt($Input.attr("data-u")) + Base64encrypt($Input.val()));
 }
});
$(document).on("keyup", ".UnlockProtectedContent", (event) => {
 const $Input = $(event.currentTarget),
           Key = Base64encrypt($Input.val()),
           SignOut = $Input.attr("data-signout") || "",
           Parent = $Input.closest(".ProtectedContent");
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: AESencrypt(LocalData("Get", "Language")),
   Token: AESencrypt(LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    const Data = RenderView(data);
    Data.View.then(response => {
     $Input.prop("disabled", true);
     setTimeout(() => {
      if(SignOut === "Yes") {
       InstantSignOut();
      }
      $(Parent).empty();
      if(Data.AddTopMargin === 1) {
       $(Parent).append("<div class='TopBarMargin'></div>\r\n");
      }
      $(Parent).append(response);
      ExecuteCommands(Data.Commands);
     }, 600);
    }).catch(error => {
     Dialog({
      "Body": "UnlockProtectedContent: Error rendering view data. Please see below for more information:",
      "Scrollable": JSON.stringify(error)
     });
    });
   }
  },
  url: base + Base64decrypt($Input.attr("data-view")) + "&Key=" + Key
 });
});
$(() => {
 $.ajax({
  error: (error) => {
   Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    Crash(data);
    return;
   } else {
    var data = JSON.parse(AESdecrypt(data));
    SaveToDatabase("Extensions", data.JSON).then(() => {
     UpdateContent(".RegSel", "[App.SwitchLanguages]", "AES");
     if($(location).attr("href") === "[App.Base]/") {
      setTimeout(() => {
       SetUIVariant(DefaultUI);
       $(".Boot").fadeOut(500);
      }, 1000);
     } else {
      $(".Boot").fadeOut(500);
     }
    }).catch(error => {
     Dialog({
      "Body": "Unable to complete the boot process. See below for more information:",
      "Scrollable": JSON.stringify(error)
     });
    });
   }
  },
  url: "[App.Base]/extensions"
 });
 setInterval(() => {
  Language = LocalData("Get", "Language");
  if(Language === "" || typeof Language === "undefined") {
   $(".RegSel").fadeIn(500);
  }
 }, 15);
 $(window).scroll(() => {
  const Offset = $(".ToggleElementOnScroll").offset().top,
            Top = $(window).scrollTop();
  if(Top < Offset) {
   $(".ToggleElementOnScroll").slideUp(500);
  } else {
   $(".ToggleElementOnScroll").slideDown(500);
  }
 });
});