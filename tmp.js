class OH {
 static DefaultContainer = ".Content";
 static DefaultUI = "[App.DefaultUI]";
 static DITkey = "[App.DITkey]";
 static Inputs = "input, number, select, textarea";
 static Language = "[App.Language]";
 static Loading = "<img src='[Media:Loading]' style='margin:0em auto;width:1em'/>";
 static UIVariant = "[App.DefaultUI]";
 static base = "[App.Base]/?_API=Web&";
 static php_js = {};
 static AddContent() {
  const Daemon = () => {
   $.ajax({
    error: (error) => {
     this.Dialog({
      "Body": "Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: this.AESencrypt(this.LocalData("Get", "Language")),
     Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: (data) => {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      this.Crash(data);
     } else {
      const Data = JSON.parse(this.AESdecrypt(data));
      if(typeof Data.View !== "undefined") {
       if(!$(".AddContent").is(":visible")) {
        $("body").append(Data.View);
        setTimeout(() => {
         $(".AddContent").fadeIn(500);
        }, 500);
       }
      }
     }
    },
    url: this.base + this.AESdecrypt("[App.AddContent]")
   });
  };
  Daemon();
  setInterval(() => {
   Daemon();
  }, 6000);
 }
 static AESdecrypt(data = "") {
  if(!data || typeof data === "undefined") {
   return data;
  } else {
   try {
    var Key = CryptoJS.enc.Base64.parse(this.DITkey),
     decrypted = "",
     hashedKey = CryptoJS.SHA256(Key),
     KeyWordList = hashedKey;
    decrypted = CryptoJS.AES.decrypt(data, KeyWordList, {
     mode: CryptoJS.mode.ECB,
     padding: CryptoJS.pad.Pkcs7
    });
    return decrypted.toString(CryptoJS.enc.Utf8);
   } catch (error) {
    console.error("AES Decryption error:", error.message);
   }
  }
 }
 static AESencrypt(data = "") {
  if(!data || typeof data === "undefined") {
   return data;
  } else {
   try {
    var Key = CryptoJS.enc.Base64.parse(this.DITkey),
     DataWordList = CryptoJS.enc.Utf8.parse(data),
     encrypted = "",
     hashedKey = CryptoJS.SHA256(Key),
     KeyWordList = hashedKey;
    encrypted = CryptoJS.AES.encrypt(DataWordList, KeyWordList, {
     mode: CryptoJS.mode.ECB,
     padding: CryptoJS.pad.Pkcs7
    });
    return encrypted.toString();
   } catch (error) {
    console.error("AES Encryption error:", error.message);
   }
  }
 }
 static Base64decrypt(data) {
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
   } catch (error) {
    console.error("Base 64 Decryption error:", error.message);
   }
  }
 }
 static Base64encrypt(data) {
  try {
   var data = typeof data === "string" ? data : JSON.stringify(data);
   data = CryptoJS.enc.Base64.stringify(CryptoJS.enc.Utf8.parse(data));
   return data;
  } catch (error) {
   console.error("Base 64 Encryption error:", error.message);
  }
 }
 static Bulletins() {
  var Daemon = () => {
   var Bulletins = ".Bulletins";
   $.ajax({
    error: (error) => {
     this.Dialog({
      "Body": "Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: this.AESencrypt(this.LocalData("Get", "Language")),
     Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: (data) => {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      this.Crash(data);
     } else {
      var Data = this.RenderView(data);
      if(Data.View > 0) {
       Bulletin = this.AESdecrypt("[App.Bulletin]");
       Bulletin = Bulletin.replaceAll("[Bulletin.Date]", "Just now");
       Bulletin = Bulletin.replaceAll("[Bulletin.From]", "[App.Name]");
       Bulletin = Bulletin.replaceAll("[Bulletin.ID]", "NewBulletins");
       Bulletin = Bulletin.replaceAll("[Bulletin.Message]", "You have " + Response + " new Bulletins!");
       Bulletin = Bulletin.replaceAll("[Bulletin.Options]", "<button class='CloseBulletins v2 v2w'>Okay</button>");
       Bulletin = Bulletin.replaceAll("[Bulletin.Picture]", "<img class='c2' src='[Media:LOGO]' style='width:100%'/>");
       $(Bulletins).html(Bulletin);
       setTimeout(() => {
        $(Bulletins).show("slide", {
         direction: "right"
        }, 500);
        setTimeout(() => {
         $(Bulletins).hide("slide", {
          direction: "right"
         }, 500);
         setTimeout(() => {
          $(Bulletins).empty();
         }, 5000);
        }, 10000);
       }, 500);
      }
     }
    },
    url: this.base + this.AESdecrypt("[App.Bulletins]")
   });
  };
  Daemon();
  setInterval(() => {
   Daemon();
  }, 120000);
 }
 static Card(data) {
  let Data = data || {},
       Action = Data.Action || "",
       Front = Data.Front || "",
       Card = "",
       ID = Data.ID || this.UUID();
  $.each($(".CloseCard, .OpenCard"), () => {
   this.disabled = true;
  });
  $("body").append("<div class='CardOverlay " + ID + " Overlay h'></div>");
  const FrontFace = (typeof Front === "object") ? this.ChangeData(Front) : Promise.resolve(Front);
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
    $.each($(".CloseCard, .OpenCard"), () => {
     this.disabled = false;
    });
    $("." + ID).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
   }, 600);
  });
 }
 static ChangeData(data) {
  let Data = data || {},
       Change = Data.ChangeData || {},
       View = "";
  if(typeof Data.Extension !== "undefined") {
   View = this.AESdecrypt(Data.Extension);
   const promises = Object.entries(Change).map(([key, value]) => {
    if($.isArray(value) || typeof value === "object") {
     return this.ChangeData(value).then(replacement => {
      View = View.replaceAll(key, replacement);
     });
    } else {
     View = View.replaceAll(key, this.PlainText({
      "BBCodes": 1,
      "Data": value
     }));
     return Promise.resolve();
    }
   });
   return Promise.all(promises).then(() => View);
  } else if(typeof Data.ExtensionID !== "undefined") {
   return this.LoadFromDatabase("Extensions", Data.ExtensionID).then(Extension => {
    if(!Extension || !Extension.Data) {
     this.Dialog({
      "Body": "Extension or Extension Data is undefined for <em>" + Data.ExtensionID + "</em>."
     });
     return "";
    } else {
     View = this.AESdecrypt(Extension.Data);
     const promises = Object.entries(Change).map(([key, value]) => {
      if($.isArray(value) || typeof value === "object") {
       return this.ChangeData(value).then(replacement => {
        View = View.replaceAll(key, replacement);
       });
      } else {
       View = View.replaceAll(key, this.PlainText({
        "BBCodes": 1,
        "Data": value
       }));
       return Promise.resolve();
      }
     });
     return Promise.all(promises).then(() => View);
    }
   }).catch(error => {
    this.Dialog({
     "Body": "ChangeData: Error retrieving extension.",
     "Scrollable": error.message
    });
    return "";
   });
  } else {
   return Promise.resolve("");
  }
 }
 static CloseCard(ID = "") {
  var Overlay = "." + ID;
  if(ID === "" || typeof ID === "undefined") {
   Overlay = ".CardOverlay:last";
  }
  $(".CloseCard, .OpenCard").each(() => {
   this.disabled = true;
  });
  $(Overlay).find(".CardFront").hide("slide", {
   direction: "down"
  }, 500);
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
 static CloseDialog(ID = "") {
  var Overlay = "." + ID;
  if(ID === "" || typeof ID === "undefined") {
   Overlay = ".DialogOverlay:last";
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
 static CloseFirSTEPTool() {
  $(".CloseFirSTEPTool, .OpenFirSTEPTool").each(() => {
   this.disabled = true;
  });
  $(".FST").hide("slide", {
   direction: "right"
  }, 500);
  if($(window).width() > 1000) {
   $(".Content").animate({
    "width": "100%"
   });
  } else {
   $(".Content").show("slide", {
    direction: "left"
   }, 500);
   $(".CloseFirSTEPTool, .OpenFirSTEPTool").each(() => {
    this.disabled = false;
   });
  }
  setTimeout(() => {
   $(".FST").empty();
  }, 500);
 }
 static CloseNetMap() {
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
     $(".NetMap").empty();
    }, 500);
   }, 500);
  }
 }
 static Crash(data = "") {
  this.Dialog({
   "Body": "An internal error has occurred and the request could not be completed. Please refer to the console for more information on this error.",
   "Header": "Crash Report",
   "Scrollable": data
  });
 }
 static DeleteContainer(button) {
  var Button = button,
   Container = $(Button).closest($(Button).attr("data-target"));
  $(Container).slideUp(500);
  setTimeout(() => {
   $(Container).remove();
  }, 500);
 }
 static Dialog(data) {
  let Data = data || {},
       Actions = "",
       ActionsList = Data.Actions || "",
       Body = Data.Body || "",
       Dialog = "",
       Header = Data.Header || "Error",
       ID = Data.ID || this.UUID(),
       NoClose = Data.NoClose || 0,
       Scrollable = Data.Scrollable || "";
  $(".CloseDialog, .OpenDialog").each(() => {
   this.disabled = true;
  });
  $("body").append("<div class='DialogOverlay " + ID + " Overlay h'></div>");
  const FrontFace = (typeof Body === "object") ? this.ChangeData(Body) : Promise.resolve(Body);
  return FrontFace.then(response => {
   if(ActionsList !== "" && typeof ActionsList !== "undefined") {
    $(ActionsList).each((key, value) => {
     Actions += value;
    });
   }
   if(NoClose === 0) {
    let Confirm = (Actions === "") ? "Okay" : "Cancel";
    Actions += "<button class='CloseDialog v2 v2w' data-id='" + ID + "'>" + Confirm + "</button>\r\n";
   }
   Dialog = "<div class='Frosted Dialog Rounded Shadowed h scr'>\r\n";
   if(Header !== "" && typeof Header !== "undefined") {
    Dialog += "<h3 class='CenterText'>" + Header + "</h3>\r\n";
   }
   if(response !== "" && typeof response !== "undefined") {
    Dialog += "<p class='CenterText'>" + response + "</p>\r\n";
   }
   if(Scrollable !== "" && typeof Scrollable !== "undefined") {
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
 static GetCreditExchange(data) {
  setInterval(() => {
   let CreditExchange = $(document).find(".CE" + data),
        Credits,
        Numeric;
   if($(CreditExchange).find(".RangeInput" + data).is(":visible")) {
    Credits = $(CreditExchange).find(".RangeInput" + data).val();
    Numeric = (Credits * 0.00001).toFixed(2);
    $(CreditExchange).find(".CreditExchange").text("Apply $" + Numeric);
    $(CreditExchange).find(".GetRangeValue").text(Credits);
   }
  }, 250);
 }
 static GetEmailValidation(data) {
  var email = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return email.test(data);
 }
 static GetRangeValue(data) {
  var Range = $(document).find(data);
  setInterval(() => {
   if($(Range).is(":visible")) {
    $(Range).next().closest(".GetRangeValue").text($(Range).val());
   }
  }, 250);
 }
 static GetSortedList(data) {
  var Response = [];
  $.each(data, (key, value) => {
   if($.type(value) === "object") {
    value = this.GetSortedList(value);
   }
   Response.push([key, value]);
  });
  return JSON.parse(JSON.stringify(Response.sort(function(a, b) {
   if(a[0] === b[0]) {
    Response = 0;
   } else {
    Response = (a[0] < b[0]) ? -1 : 1;
   }
   return Response;
  }).reduce(function(key, value) {
   key[value] = value;
   return key;
  }, {})));
 }
 static GoToParent(Data) {
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
  } catch (error) {
   this.Dialog({
    "Body": "An error occurred while switching to the parent view.",
    "Scrollable": error.message
   });
  }
 }
 static GoToView(Container, Container2, Data = "") {
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
 static Encrypt(data) {
  if($.isArray(data) && typeof data !== "undefined") {
   $.each(data, (key, input) => {
    input.value = this.AESencrypt(input.value);
   });
   return data;
  } else {
   this.Dialog({
    "Body": "The encoder expects a populated list."
   });
  }
 }
 static ExecuteCommands(Commands = "", Executed = "No") {
  if(typeof Commands === "object" && Executed === "No") {
   $.each(Commands, (Key, Command) => {
    const AES = Command.AES || "No",
              Name = Command.Name || "",
              Parameters = Command.Parameters || {};
    let ParameterCount = 0;
    ParameterCount = Object.keys(Parameters).length;
    if(Name === "AddContent") {
     this.AddContent();
    } else if(Name === "Bulletins") {
     this.Bulletins();
    } else if(Name === "GetCreditExchange" && ParameterCount === 1) {
     this.GetCreditExchange(Parameters[0]);
    } else if(Name === "RefreshCoverPhoto" && ParameterCount === 2) {
     this.RefreshCoverPhoto(Parameters[0], Parameters[1]);
    } else if(Name === "RenderInputs" && ParameterCount === 2) {
     this.RenderInputs(Parameters[0], Parameters[1]);
    } else if(Name === "RenderVisibilityFilter" && ParameterCount === 2) {
     this.RenderVisibilityFilter(Parameters[0], Parameters[1]);
    } else if(Name === "SignIn" && ParameterCount === 1) {
     this.SignIn(Command.Parameters[0]);
    } else if(Name === "UpdateContent" && ParameterCount === 2) {
     this.UpdateContent(Parameters[0], Parameters[1]);
    } else if(Name === "UpdateContentAES" && ParameterCount === 2) {
     this.UpdateContent(Parameters[0], Parameters[1], "AES");
    } else if(Name === "UpdateCoverPhoto" && ParameterCount === 2) {
     this.UpdateCoverPhoto(Parameters[0], Parameters[1]);
    } else if(Name === "UpdateContentRecursive" && ParameterCount === 3) {
     this.UpdateContentRecursive(Parameters[0], Parameters[1], Parameters[2]);
    } else if(Name === "UpdateContentRecursiveAES" && ParameterCount === 3) {
     this.UpdateContentRecursive(Parameters[0], Parameters[1], Parameters[2], "AES");
    }
   });
  }
 }
 static FST(data) {
  const Data = data || "";
  $(".FST").html("<div class='TopBarMargin'></div>\r\n");
  $(".FST").append(Data);
  $(".FST").find("input[type=text], textarea").filter(":enabled:visible:first").focus();
  setTimeout(() => {
   if($(window).width() > 1000) {
    $(".Content").animate({
     "width": "66.66%"
    });
   } else {
    $(".Content").hide("slide", {
     direction: "left"
    }, 500);
   }
   $(".FST").show("slide", {
    direction: "right"
   }, 500);
  }, 500);
 }
 static InstantSignOut() {
  setTimeout(() => {
   this.LocalData("Purge", "SecurityKey");
   this.SetUIVariant(this.DefaultUI);
  }, 1000);
 }
 static LiveView(input) {
  var Daemon = () => {
   var Input = $(document).find(input),
    DLC = $(Input).val(),
    Preview = $(Input).attr("data-preview-destination");
   if($(Input).length && $(Preview).length) {
    if($(Preview).is(":visible")) {
     $.ajax({
      error: (error) => {
       this.Dialog({
        "Body": "Data retrieval error, please see below.",
        "Scrollable": JSON.stringify(error)
       });
      },
      headers: {
       Language: this.AESencrypt(this.LocalData("Get", "Language")),
       Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
      },
      method: "POST",
      success: (data) => {
       if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
        this.Crash(data);
       } else {
        const Data = this.RenderView(data);
        if(Data.View !== "" && typeof Data.View !== "undefined") {
         Data.View.then(response => {
          $(Preview).html(response);
          this.ExecuteCommands(Data.Commands);
         }).catch(error => {
          this.Dialog({
           "Body": "LiveView: Error rendering view data. Please see below for more information:",
           "Scrollable": error.message
          });
         });
        }
       }
      },
      url: this.base + this.Base64decrypt($(Input).attr("data-live-view")) + DLC
     });
    }
   }
  };
  Daemon();
  setInterval(() => {
   Daemon();
  }, 15000);
 }
 static LocalData(action = "Get", identifier = "", data = {}) {
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
 static LoadFromDatabase(Store, ID) {
  if(typeof Store !== "string" || Store.trim() === "") {
   return Promise.reject(new Error("The Store Identifier is missing."));
  }
  if(ID === undefined || ID === null) {
   return Promise.reject(new Error("The Data Identifier is missing."));
  }
  return new Promise((resolve, reject) => {
   const openRequest = indexedDB.open("OuterHaven");
   openRequest.onupgradeneeded = () => {
    const db = openRequest.result;
    if(!db.objectStoreNames.contains(Store)) {
     db.createObjectStore(Store, {
      keyPath: "ID"
     });
    }
   };
   openRequest.onerror = () => reject(new Error("Failed to open database: " + openRequest.error));
   openRequest.onsuccess = () => {
    const db = openRequest.result;
    let transaction;
    try {
     transaction = db.transaction(Store, "readonly");
    } catch (error) {
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
 static OpenCard(View, Encryption = "") {
  if(Encryption === "AES") {
   View = this.AESdecrypt(View);
  } else {
   View = this.Base64decrypt(View);
  }
  $.ajax({
   error: (error) => {
    this.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: this.AESencrypt(this.LocalData("Get", "Language")),
    Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     this.Crash(data);
    } else {
     this.RenderView(data);
    }
   },
   url: this.base + View
  });
 }
 static OpenDialog(View, Encryption = "") {
  if(Encryption === "AES") {
   View = this.AESdecrypt(View);
  } else {
   View = this.Base64decrypt(View);
  }
  $.ajax({
   error: (error) => {
    this.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: this.AESencrypt(this.LocalData("Get", "Language")),
    Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     this.Crash(data);
    } else {
     this.RenderView(data);
    }
   },
   url: this.base + View
  });
 }
 static OpenFirSTEPTool(Ground, FirSTEPTool) {
  if(Ground !== "" && typeof Ground !== "undefined") {
   $.ajax({
    error: (error) => {
     this.Dialog({
      "Body": "OpenFirSTEPTool: Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: this.AESencrypt(this.LocalData("Get", "Language")),
     Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: (data) => {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      this.Crash(data);
     } else {
      const Data = this.RenderView(data);
      if(Data.View !== "" && typeof Data.View !== "undefined") {
       Data.View.then(response => {
        if(Data.AddTopMargin === 1) {
         $(this.DefaultContainer).append("<div class='TopBarMargin'></div>\r\n");
        }
        $(this.DefaultContainer).html(response);
        $(this.DefaultContainer).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
        this.ExecuteCommands(Data.Commands);
       }).catch(error => {
        this.Dialog({
         "Body": "OpenFirSTEPTool: Error rendering view data. Please see below for more information:",
         "Scrollable": error.message
        });
       });
      }
     }
    },
    url: this.base + this.AESdecrypt(Ground)
   });
  } if(FirSTEPTool !== "" && typeof FirSTEPTool !== "undefined") {
   $.ajax({
    error: (error) => {
     this.Dialog({
      "Body": "OpenFirSTEPTool: Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: this.AESencrypt(this.LocalData("Get", "Language")),
     Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: (data) => {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      this.Crash(data);
     } else {
      const Data = this.RenderView(data);
      if(Data.View !== "" && typeof Data.View !== "undefined") {
       Data.View.then(response => {
        this.FST(response);
        this.ExecuteCommands(Data.Commands);
       }).catch(error => {
        this.Dialog({
         "Body": "OpenFirSTEPTool: Error rendering view data. Please see below for more information:",
         "Scrollable": error.message
        });
       });
      }
     }
    },
    url: this.base + this.AESdecrypt(FirSTEPTool)
   });
  }
 }
 static OpenNetMap(View, Encryption = "") {
  if(Encryption === "AES") {
   View = this.AESdecrypt(View);
  } else {
   View = this.Base64decrypt(View);
  }
  $.ajax({
   error: (error) => {
    this.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: this.AESencrypt(this.LocalData("Get", "Language")),
    Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     this.Crash(data);
    } else {
     const Data = this.RenderView(data);
     if(Data.View !== "" && typeof Data.View !== "undefined") {
      Data.View.then(response => {
       $(".CloseNetMap, .OpenNetMap").each(() => {
        this.disabled = true;
       });
       $(".NetMap").html(response);
       $(".NetMap").find("input[type=text], textarea").filter(":enabled:visible:first").focus();
       setTimeout(() => {
        $(".CloseNetMap, .OpenNetMap").each(() => {
         this.disabled = false;
        });
        $(".NetMap").fadeIn(500);
        $(".NetMap .ToggleAnimation").slideUp(1000);
        this.ExecuteCommands(Data.Commands);
       }, 500);
      }).catch(error => {
       this.Dialog({
        "Body": "OpenNetMap: Error rendering view data. Please see below for more information:",
        "Scrollable": error.message
       });
      });
     }
    }
   },
   url: this.base + View
  });
 }
 static RefreshCoverPhoto(container, data = {}, disabled = "false") {
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
 static RenderDesignView(container) {
  var Container = container || {},
   DesignView = $($(Container).attr("data-in")).val();
  if($(Container).is(":visible")) {
   DesignView = this.Base64encrypt(encodeURIComponent(DesignView));
   $.ajax({
    error: (error) => {
     this.Dialog({
      "Body": "Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: this.AESencrypt(this.LocalData("Get", "Language")),
     Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: (data) => {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      this.Crash(data);
     } else {
      const Data = this.RenderView(data);
      if(Data.View !== "" && typeof Data.View !== "undefined") {
       Data.View.then(response => {
        $(Container).html(response);
        $.each($(Container).find("button, input, select, textarea"), () => {
         this.disabled = true;
        });
        $(Container).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
        this.ExecuteCommands(Data.Commands);
       }).catch(error => {
        this.Dialog({
         "Body": "RenderDesignView: Error rendering view data. Please see below for more information:",
         "Scrollable": error.message
        });
       });
      }
     }
    },
    url: this.base + this.Base64decrypt($(Container).attr("data-u")) + DesignView
   });
  }
 }
 static RenderInputs(Container, Data) {
  var Container = Container || this.DefaultContainer,
        Data = Data || {};
  if(Container !== "" && Data !== {}) {
   $(Container).empty();
   $.each(Data, (key, input) => {
    var Attributes,
          Input = input || {},
          OptionGroup = Input["OptionGroup"] || {},
          OptionGroupLabel,
          Options = Input["Options"] || {},
          RenderInput = "",
          Type = Input["Type"] || "Text";
    Attributes = Input["Attributes"] || {};
    if(Attributes !== {} && Type !== "") {
     var OptionGroupLabel = "",
           OptionGroupList = "",
           RenderInput = "",
           RenderInputAttributes = "",
           Selected = "";
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
      if(Object.keys(OptionGroup).length === 0) {
       this.Dialog({
        "Body": "RenderInputs: The <em>select</em> input option group is empty."
       });
      } else {
       $.each(OptionGroup, (option, text) => {
        Selected = (Input["Value"] === option) ? " selected" : "";
        OptionGroupList += "<option value='" + option + "'" + Selected + ">" + text + "</option>\r\n";
       });
       OptionGroupLabel = Options["HeaderText"] || Input["Title"];
       RenderInput = "<select class='LI v2 v2w' name='" + Input["Name"] + "'>\r\n";
       RenderInput += "<optgroup label='" + OptionGroupLabel + "'>\r\n";
       RenderInput += OptionGroupList + "\r\n";
       RenderInput += "</optgroup>\r\n";
       RenderInput += "</select>\r\n";
      }
     } else if(Type === "Text") {
      var TextType = Attributes["type"] || "",
            TextValue = (TextType === "hidden") ? Input["Value"] : this.AESdecrypt(Input["Value"]);
      RenderInput = "<input" + RenderInputAttributes + " value='" + TextValue + "'/>\r\n";
     } else if(Type === "TextBox") {
      RenderInput = "<textarea " + RenderInputAttributes + ">" + this.AESdecrypt(Input["Value"]) + "</textarea>\r\n";
      if(Options["WYSIWYG"] === 1) {
       RenderInput = "<textarea " + RenderInputAttributes + " rows='40'>" + this.AESdecrypt(Input["Value"]) + "</textarea>\r\n";
       $.ajax({
        error: (error) => {
         this.Dialog({
          "Body": "Data retrieval error, please see below.",
          "Scrollable": JSON.stringify(error)
         });
        },
        headers: {
         Language: this.AESencrypt(this.LocalData("Get", "Language")),
         Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
        },
        method: "POST",
        success: (data) => {
         if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
          this.Crash(data);
         } else {
          let WData = this.RenderView(data);
          this.ExecuteCommands(WData.Commands);
          WData.View.then(response => {
           WYSIWYG = response;
           WYSIWYG = WYSIWYG.replaceAll("[WYSIWYG.ID]", Attributes["data-editor-identifier"]);
           WYSIWYG = WYSIWYG.replaceAll("[WYSIWYG.TextBox]", RenderInput);
           RenderInput = WYSIWYG;
          }).catch(error => {
           this.Dialog({
            "Body": "WYSIWYG: Error rendering view data. Please see below:",
            "Scrollable": error.message
           });
          });
         }
        },
        url: this.base + this.AESdecrypt("[App.WYSIWYG]")
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
   $(Container).parent().find("input[type=text], textarea").filter(":enabled:visible:first").focus();
  }
 }
 static RenderView(data) {
  let Data = JSON.parse(this.AESdecrypt(data)),
       AccessCode = Data.AccessCode || "Denied",
       AddTopMargin = Data.AddTopMargin || 1,
       Commands = Data.Commands || "",
       NewVariant = Data.SetUIVariant || "",
       ResponseType = Data.ResponseType || "Dialog",
       Success = Data.Success || "",
       Title = Data.Title || "[App.Name]",
       View = Data.View || "";
  $(document).prop("title", Title);
  this.SetUIVariant(NewVariant);
  if(Data.Card !== "" && typeof Data.Card !== "undefined") {
   this.Card(Data.Card);
   setTimeout(() => {
    this.ExecuteCommands(Commands);
   }, 600);
  }
  if(Data.Dialog !== "" && typeof Data.Dialog !== "undefined") {
   this.Dialog(Data.Dialog);
   setTimeout(() => {
    this.ExecuteCommands(Commands);
   }, 600);
  }
  if(typeof View === "object") {
   View = this.ChangeData(View);
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
 static PlainText(data) {
  let View = data.Data || "";
  if(View !== "" && typeof View === "string") {
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
    View = this.AESdecode(View);
   } if(data.Encode && data.Encode === 1) {
    View = this.AESencode(View);
   }
  }
  return View;
 }
 static RenderVisibilityFilter(Container, Data) {
  var Container = Container || this.DefaultContainer,
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
   this.RenderInputs(Container, [{
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
   }]);
  }
 }
 static RenderVisibilityFilters(Container, Data) {
  var Container = Container || this.DefaultContainer,
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
   this.RenderInputs(Container, Inputs);
  }
 }
 static ReSearch(input) {
  var Bar = input || {},
   Container = $(Bar).parent().find(".SearchContainer") || {},
   GridColumns = $(Bar).attr("data-columns") || "1",
   List = $(Bar).attr("data-list") || "",
   Offset = 0,
   Processor,
   Query = $(Bar).val() || "",
   End;
  if(Bar === {} || typeof Bar === "undefined") {
   this.Dialog({
    "Body": "ReSearch: The source input is missing."
   });
  } else if(Container === {} || typeof Container === "undefined") {
   this.Dialog({
    "Body": "ReSearch: The list container is missing."
   });
  } else if(List === "" || typeof List === "undefined") {
   this.Dialog({
    "Body": "ReSearch: The list source is missing."
   });
  }
  Processor = this.base + this.Base64decrypt(List) + "&query=" + this.Base64encrypt($(Bar).val());
  $(Container).empty();
  $.ajax({
   error: (error) => {
    this.Dialog({
     "Body": "ReSearch: Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: this.AESencrypt(this.LocalData("Get", "Language")),
    Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     this.Crash(data);
    } else {
     var Data = JSON.parse(this.AESdecrypt(data)),
      AccessCode = Data.AccessCode,
      Extension = Data.Extension,
      ExtensionID = Data.ExtensionID,
      Response = Data.Response,
      SearchID = "SearchList" + this.UUID(),
      Grid = GridColumns;
     if(AccessCode !== "Accepted") {
      $(Container).html(this.Base64decrypt(Response.NoResults));
     }
     if(Data.ExtensionID) {
      Extension = this.LoadFromDatabase("Extensions", Data.ExtensionID);
     } else if(Data.Extension) {
      Extension = Promise.resolve(this.AESdecrypt(Data.Extension));
     } else {
      this.Dialog({
       "Body": "ReSearch: Neither ExtensionID nor Extension is provided."
      });
      return;
     }
     Extension.then(response => {
      if(Data.ExtensionID) {
       response = this.AESdecrypt(response.Data);
      }
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
      $(Container).html("<div class='" + Grid + " " + SearchID + "'>" + this.Loading + "</div>");
      Container = $(Container).find("." + SearchID);
      var List = this.GetSortedList(Response.List),
       ListItems = 0,
       check = (List !== {} && typeof List !== "undefined") ? 1 : 0;
      check = (typeof List === "object" || check === 1) ? 1 : 0;
      if(check === 1) {
       check = (Query !== "" && typeof Query !== "undefined") ? 1 : 0;
       $("." + SearchID).empty();
       for(var i in List) {
        var KeyCheck = ($.type(List[i][0]) !== "undefined") ? 1 : 0,
         ValueCheck = ($.type(List[i][1]) !== "undefined") ? 1 : 0;
        if(KeyCheck === 1 && ValueCheck === 1) {
         var Search = (check === 0) ? 1 : 0,
          Result = response || "",
          value = List[i][1] || {};
         if(value !== {} && typeof value !== "undefined") {
          for(var j in value) {
           if(typeof Result === 'string') {
            Result = Result.replaceAll(value[j][0], value[j][1]);
           }
          }
          if(Result.search(Query) > -1) {
           Search += 1;
          }
          if(Result.toLowerCase().search(Query.toLowerCase()) > -1) {
           Search += 1;
          }
          if(Search > 0) {
           ListItems += 1;
           $(Container).append(Result);
          }
         }
        }
       }
       if(ListItems === 0) {
        $(Container).html(this.Base64decrypt(Response.NoResults));
       } else {
        setInterval(() => {
         if($(Container).is(":visible") && $(Container).length && End === 0) {
          $.ajax({
           error: (error) => {
            this.Dialog({
             "Body": "ReSearch: Data retrieval error during infinite scroll, please see below.",
             "Scrollable": JSON.stringify(error)
            });
           },
           headers: {
            Language: this.AESencrypt(this.LocalData("Get", "Language")),
            Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
           },
           method: "POST",
           success: (data) => {
            if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
             this.Crash(data);
            } else {
             var Data = JSON.parse(this.AESdecrypt(data)),
              Response = Data.Response;
             End = Response.End || 0;
             if(End === 0) {
              Offset += Response.Limit;
             }
             var List = this.GetSortedList(Response.List),
              check = (List !== {} && typeof List !== "undefined") ? 1 : 0;
             check = (typeof List === "object" || check === 1) ? 1 : 0;
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
                 for(var j in value) {
                  Result = Result.replaceAll(value[j][0], this.Base64decrypt(value[j][1]));
                 }
                 if(Result.search(Query) > -1) {
                  Search += 1;
                 }
                 if(Result.toLowerCase().search(Query.toLowerCase()) > -1) {
                  Search += 1;
                 }
                 if(Search > 0) {
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
     }).catch(error => {
      this.Dialog({
       "Body": "ReSearch: Error loading extension. Please see below:",
       "Scrollable": error.message
      });
     });
    }
   },
   url: Processor
  });
 }
 static SaveToDatabase(Store, Data) {
  if(typeof Store !== "string" || Store.trim() === "") {
   return Promise.reject(new Error("The Store Identifier is missing."));
  }
  if(!Array.isArray(Data)) {
   return Promise.reject(new Error("Data must be an array."));
  }
  for(const pair of Data) {
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
    db.createObjectStore(Store, {
     keyPath: "ID"
    });
   };
   openRequest.onerror = () => reject(openRequest.error);
   openRequest.onsuccess = () => {
    const db = openRequest.result;
    const transaction = db.transaction(Store, "readwrite");
    const store = transaction.objectStore(Store, {
     keyPath: "ID"
    });
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
 static SetUIVariant(NewVariant = this.DefaultUI) {
  if($(location).attr("href") === "[App.Base]/" && NewVariant !== this.UIVariant) {
   this.UIVariant = NewVariant;
   $(".SideBar").hide("slide", {
    direction: "left"
   }, 500);
   $(".TopBar .MenuContainer").hide("slide", {
    direction: "up"
   }, 500);
   setTimeout(() => {
    if(NewVariant === "0") {
     $(".TopHome, .TopSearchBar").hide("slide", {
      direction: "up"
     }, 500);
     $(".TopBar").hide("slide", {
      direction: "up"
     }, 500);
     $(".TopBar .MenuContainer").hide("slide", {
      direction: "up"
     }, 500);
     $(".TopBarClassic").show("slide", {
      direction: "up"
     }, 500);
    } else if(NewVariant === "1") {
     $(".SideBar").hide("slide", {
      direction: "left"
     }, 500);
     $(".TopHome, .TopSearchBar").hide("slide", {
      direction: "up"
     }, 500);
     $(".TopBarClassic").hide("slide", {
      direction: "up"
     }, 500);
     $(".TopBar").show("slide", {
      direction: "down"
     }, 500);
    } else if(NewVariant === "2") {
     $(".TopBar .MenuContainer").hide("slide", {
      direction: "up"
     }, 500);
     $(".TopBar").hide("slide", {
      direction: "up"
     }, 500);
     $(".TopBarClassic").hide("slide", {
      direction: "up"
     }, 500);
     $(".TopHome").show("slide", {
      direction: "up"
     }, 500);
     $(".TopSearchBar").show("slide", {
      direction: "down"
     }, 500);
    }
   }, 600);
  }
 }
 static SignIn(SecurityKey = "") {
  $.ajax({
   error: (error) => {
    this.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: this.AESencrypt(this.LocalData("Get", "Language")),
    Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     this.Crash(data);
    } else {
     const Data = JSON.parse(this.AESdecrypt(data));
     this.ExecuteCommands(Data.Commands);
     this.LocalData("Save", "SecurityKey", SecurityKey);
     this.GoToParent("MainView");
    }
   },
   url: this.base + this.AESdecrypt("[App.MainUI]")
  });
 }
 static SignOut() {
  this.InstantSignOut();
  setTimeout(() => {
   $.ajax({
    error: (error) => {
     this.Dialog({
      "Body": "Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: this.AESencrypt(this.LocalData("Get", "Language")),
     Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: (data) => {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      this.Crash(data);
     } else {
      const Data = this.RenderView(data);
      this.ExecuteCommands(Data.Commands);
      Data.View.then(response => {
       $(this.DefaultContainer).html(response);
       $(this.DefaultContainer).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
       this.UpdateContent(".Menu", "[App.Menu]", "AES");
       $(".AddContent").fadeOut(500);
       $(".SideBar").hide("slide", {
        direction: "left"
       }, 500);
       $(".TopBar .MenuContainer").slideUp(500);
       setTimeout(() => {
        $(".AddContent").remove();
       }, 600);
      }).catch(error => {
       this.Dialog({
        "Body": "SignOut: Error rendering view data. Please see below:",
        "Scrollable": error.message
       });
      });
     }
    },
    url: this.base + this.AESdecrypt("[App.Gateway]")
   });
  }, 600);
 }
 static UpdateButton(button, data) {
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
 static UpdateContent(Container, View, Encryption = "") {
  Container = Container || this.DefaultContainer;
  if(Encryption === "AES") {
   View = this.AESdecrypt(View);
  } else {
   View = this.Base64decrypt(View);
  } if($(Container).html() === "") {
   $(Container).html("<h2 class='CenterText InnerMargin'>" + this.Loading + "</h2>\r\n");
  }
  setTimeout(() => {
   $.ajax({
    error: (error) => {
     this.Dialog({
      "Body": "Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: this.AESencrypt(this.LocalData("Get", "Language")),
     Token: this.AESencrypt(this.LocalData("Get", "SecurityKey"))
    },
    method: "POST",
    success: (data) => {
     if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
      this.Crash(data);
     } else {
      const Data = this.RenderView(data);
      if(Data.View !== "" && typeof Data.View !== "undefined") {
       Data.View.then(response => {
        $(Container).empty();
        if(Data.AddTopMargin === 1) {
         $(Container).append("<div class='TopBarMargin'></div>\r\n");
        }
        $(Container).append(response);
        $(Container).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
        this.ExecuteCommands(Data.Commands);
       }).catch(error => {
        this.Dialog({
         "Body": "UpdateContent: Error rendering view data. Please see below:",
         "Scrollable": error.message
        });
       });
      }
     }
    },
    url: this.base + View
   });
  }, 600);
 }
 static UpdateContentRecursive(Container, View, Interval = 6000, Encryption = "") {
  this.UpdateContent(Container, View, Encryption);
  setInterval(() => {
   this.UpdateContent(Container, View, Encryption);
  }, Interval);
 }
 static UpdateCoverPhoto(Container, Image) {
  var Container = Container || this.DefaultContainer,
        Image = Image || "[Media:CP]";
  if(Image !== "" && typeof Image !== "undefined") {
   $(Container).css({
    "background": "url('" + Image + "') no-repeat center center fixed",
    "background-size": "cover",
    "transition": "background 1s ease-in-out"
   });
  }
 }
 static UpdateUIVariant(NewUIVariant) {
  var NewUIVariant = NewUIVariant || this.UIVariant;
  this.SetUIVariant(NewUIVariant);
  $(".PersonalUIVariant").val(NewUIVariant);
 }
 static Upload(Button) {
  let Form = $(Button).attr("data-form"),
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
   let Data = new FormData(),
        Files = $(Form).find(".FileList")[0].files,
        Inputs = $(Form).find(Inputs).serializeArray(),
        Request = new XMLHttpRequest();
   for(var i = 0; i < Files.length; i++) {
    if(Files[i].size > Math.round(500000 * 100)) {
     console.log("The media file " + Files[i].name + " comes in at " + Files[i].size + ". The maximum allowed file size is 500MB.");
    } else {
     Data.append("Uploads[" + i + "]", Files[i]);
    }
   }
   for(var i = 0; i < Inputs.length; i++) {
    Data.append(Inputs[i].name, this.AESencrypt(Inputs[i].value));
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
     this.Crash(data);
    } else {
     var AccessCode = "Denied",
      Class,
      Passed,
      Response,
      Type,
      Data = JSON.parse(this.AESdecrypt(data));
     AccessCode = Data.AccessCode || AccessCode;
     Response = Data.JSON || {};
     Type = Data.ResponseType || "Dialog";
     if(Response === "" || typeof Response === "undefined") {
      this.Dialog({
       "Body": "<em>[App.Name]</em> returned an empty response. Check the processor within the following URI fragment: " + Processor + "."
      });
     } else {
      if(AccessCode === "Denied") {
       this.Dialog({
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
   }.bind(this), false);
   Request.open("POST", this.base + this.Base64decrypt(Processor), true);
   Request.setRequestHeader("Language", this.Base64encrypt(this.LocalData("Get", "Language")));
   Request.setRequestHeader("Token", this.Base64encrypt(this.LocalData("Get", "SecurityKey")));
   Request.send(Data);
  }
 }
 static UUID() {
  var UUID = Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
  UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
  UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
  UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
  UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
  UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
  UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
  UUID += Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1) + "-";
  UUID += this.uniqid();
  return UUID;
 }
 static W(link, target) {
  var W = window.open(link, target);
  W.focus();
 }
 static uniqid(prefix = "", more_entropy) {
  if(typeof prefix === "undefined") {
   prefix = "";
  }
  var retId,
   formatSeed = function(seed, reqWidth) {
    seed = parseInt(seed, 10).toString(16);
    if(reqWidth < seed.length) {
     return seed.slice(seed.length - reqWidth);
    }
    if(reqWidth > seed.length) {
     return Array(1 + (reqWidth - seed.length)).join('0') + seed;
    }
    return seed;
   };
  if(!this.php_js.uniqidSeed) {
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
}
$(document).on("change", "select[name='ProductQuantity']", (event) => {
 const $Input = $(event.currentTarget),
  Price = $Input.parent().find(".AddToCart").text(),
  Quantity = $Input.find("option:selected").val();
 Price = Price.replace("$", "");
 Price = parseInt(Price) * parseInt(Quantity);
 $Input.parent().find(".AddToCart").text("$" + Price);
});
$(document).on("change", ".UpdateRangeValue", (event) => {
 const $Input = $(event.currentTarget),
  Price = $Input.parent().find(".AddToCart").text(),
  Quantity = $Input.find("option:selected").val();
 Price = Price.replace("$", "");
 Price = parseInt(Price) * parseInt(Quantity);
 $Input.parent().find(".AddToCart").text("$" + Price);
});
$(document).on("click", ".Attach", (event) => {
 const $Button = $(event.currentTarget),
  $Input = $(document).find(OH.Base64decrypt($Button.attr("data-input"))),
  Media = $Button.attr("data-media") || "";
 $Button.prop("disabled", true);
 if(!$Input.length) {
  OH.Dialog({
   "Body": "Failed to find the attachment input. Here is the source data: " + $Button.attr("data-input") + "."
  });
 } else if(!Media.length) {
  OH.Dialog({
   "Body": "Failed to find the attachment media. Here is the source data: " + Media + "."
  });
 } else {
  $Input.val(Media);
  $Button.text(OH.Loading);
  OH.CloseCard();
 }
});
$(document).on("click", ".Clone", (event) => {
 let $Button = $(event.currentTarget),
      CloneID = "Clone" + OH.UUID(),
      Destination = $Button.attr("data-destination"),
      Source = $($Button.attr("data-source")).text();
 Source = OH.Base64decrypt(Source.trim());
 Source = Source.replaceAll("[Clone.ID]", CloneID);
 $(Destination).append(Source);
});
$(document).on("click", ".CloneAttachments", (event) => {
 const $Button = $(event.currentTarget),
  CloneID = "AttachmentClone" + OH.UUID(),
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
      pair[1] = OH.Base64decrypt(pair[1]);
      pair[1] = pair[1].replaceAll("[Clone.ID]", cloneID);
      pair[1] = OH.Base64encrypt(pair[1]);
     }
     link += "&" + pair[0] + "=" + pair[1];
    }
   }
   return link;
  },
  RemoveAfterUse = $Button.attr("data-remove") || "off",
  Source = $($Button.attr("data-source")).text();
 Source = OH.Base64decrypt(Source.trim());
 Source = Source.replaceAll("[Clone.ID]", CloneID);
 AttachmentList = $(Source).find(".AttachmentList" + CloneID).attr("data-view");
 AttachmentList = OH.Base64decrypt(AttachmentList);
 AttachmentList = AttachmentList.replaceAll("[Clone.ID]", CloneID);
 AttachmentList = InjectCloneID(CloneID, AttachmentList);
 $(Destination).append(Source);
 $(".AttachmentList" + CloneID).attr("data-view", OH.Base64encrypt(AttachmentList));
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
   OH.CloseCard();
  }, 500);
 });
});
$(document).on("click", ".CloseAllDialogs", () => {
 $(".DialogOverlay").each(() => {
  setTimeout(() => {
   OH.CloseDialog();
  }, 500);
 });
});
$(document).on("click", ".CloseAllFirSTEPTools", () => {
 OH.CloseAllFirSTEPTools();
});
$(document).on("click", ".CloseBottomBar", () => {
 $(".BottomBar").hide("slide", {
  direction: "down"
 }, 500);
 setTimeout(() => {
  $(".BottomBar").remove();
 }, 500);
});
$(document).on("click", ".CloseBulletins", () => {
 $(".Bulletins").hide("slide", {
  direction: "right"
 }, 500);
 setTimeout(() => {
  $(".Bulletins").empty();
 }, 600);
});
$(document).on("click", ".CloseCard", (event) => {
 OH.CloseCard($(event.currentTarget).attr("data-id"));
});
$(document).on("click", ".CloseDialog", (event) => {
 OH.CloseDialog($(event.currentTarget).attr("data-id"));
});
$(document).on("click", ".CloseFirSTEPTool", (event) => {
 OH.CloseFirSTEPTool();
});
$(document).on("click", ".CloseNetMap", () => {
 OH.CloseNetMap();
});
$(document).on("click", ".CreditExchange", (event) => {
 const $Button = $(event.currentTarget),
           Form = ".CE" + $Button.attr("data-id"),
           Price = $Button.attr("data-p");
 Price = $(Form).find(".RangeInput" + $Button.attr("data-id")).val();
 if($.isNumeric(Price)) {
  $Button.prop("disabled", "true");
  $.ajax({
   error: (error) => {
    OH.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
    Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     OH.Crash(data);
    } else {
     const Data = OH.RenderView(data);
     if(Data.AccessCode === "Denied") {
      $Button.prop("disabled", false);
     }
    }
   },
   url: OH.base + OH.Base64decrypt($Button.attr("data-u")) + OH.Base64encrypt(Price)
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
  OH.DeleteContainer($Button);
 } else {
  $.ajax({
   error: (error) => {
    OH.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
    Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     OH.Crash(data);
    } else {
     const Data = OH.RenderView(data);
     if(Data.AccessCode === "Denied") {
      $Button.text("Try Later");
     } else {
      $Button.text("Done!");
      OH.DeleteContainer($Button);
     }
    }
   },
   url: OH.base + OH.Base64decrypt(Processor)
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
  OH.Dialog({
   "Body": "No media to download."
  });
 } else {
  Media = OH.Base64decrypt(Media).split(";");
  $.each(Media, (key, value) => {
   $.ajax({
    data: {
     FilePath: value
    },
    error: (error) => {
     OH.Dialog({
      "Body": "Data retrieval error, please see below.",
      "Scrollable": JSON.stringify(error)
     });
    },
    headers: {
     Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
     Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
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
    url: OH.base + OH.Base64decrypt(Downloader),
    xhrFields: {
     responseType: "blob"
    }
   });
  });
 }
});
$(document).on("click", ".GoToParent", (event) => {
 let $Button = $(event.currentTarget),
      Data = $Button.attr("data-type");
 OH.GoToParent(Data);
});
$(document).on("click", ".GoToView", (event) => {
 let $Button = $(event.currentTarget),
      Data = $Button.attr("data-type").split(";"),
      Encryption = $Button.attr("data-encryption") || "",
      ID = Data[0] || "",
      Parent = $(".ParentPage" + ID).parent(),
      View = Data[1] || "";
 if(Encryption === "AES") {
  View = OH.AESdecrypt(View);
 } else {
  View = OH.Base64decrypt(View);
 }
 setTimeout(() => {
  $.ajax({
   error: (error) => {
    OH.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
    Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     OH.Crash(data);
    } else {
     const Data = OH.RenderView(data);
     Data.View.then(response => {
      OH.GoToView("ParentPage" + ID, "ViewPage" + ID, response);
      OH.ExecuteCommands(Data.Commands);
     }).catch(error => {
      OH.Dialog({
       "Body": "GoToView: Error rendering view data. Please see below:",
       "Scrollable": error.message
      });
     });
    }
   },
   url: OH.base + View
  });
 }, 500);
});
$(document).on("click", ".InstantSignOut", (event) => {
 OH.InstantSignOut();
});
$(document).on("click", ".MarkAsRead", (event) => {
 $.ajax({
  error: (error) => {
   OH.Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
   Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    OH.Crash(data);
   } else {
    OH.RenderView(data);
   }
  },
  url: OH.base + OH.Base64decrypt($(event.currentTarget).attr("data-MAR"))
 });
});
$(document).on("click", ".Menu button", (event) => {
 $(".SideBar").hide("slide", {
  direction: "left"
 }, 500);
 $(".TopBar .MenuContainer").slideUp(500);
});
$(document).on("click", ".OpenBottomBar", (event) => {
 const $Button = $(event.currentTarget),
  View = $Button.attr("data-view") || "";
 if(View !== "" && typeof View !== "undefined") {
  $.ajax({
   error: (error) => {
    OH.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
    Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     OH.Crash(data);
    } else {
     const Data = OH.RenderView(data);
     if(Data.View !== "" && typeof Data.View !== "undefined") {
      Data.View.then(response => {
       $("body").append(response);
       $("body").find("input[type=text], textarea").filter(":enabled:visible:first").focus();
       $(".BottomBar").show("slide", {
        direction: "down"
       }, 500);
       OH.ExecuteCommands(Data.Commands);
      }).catch(error => {
       OH.Dialog({
        "Body": "OpenBottomBar: Error rendering view data. Please see below:",
        "Scrollable": error.message
       });
      });
     }
    }
   },
   url: OH.base + OH.Base64decrypt(View)
  });
 }
});
$(document).on("click", ".OpenCard", (event) => {
 const $Button = $(event.currentTarget),
           Encryption = $Button.attr("data-encryption") || "",
           View = $Button.attr("data-view") || "";
 OH.OpenCard(View, Encryption);
});
$(document).on("click", ".OpenCardFromJSON", (event) => {
 const $Button = $(event.currentTarget),
           Data = $Button.attr("data-json") || OH.Base64encrypt({});
 OH.Card(OH.Base64decrypt(Data));
});
$(document).on("click", ".OpenDialog", (event) => {
 const $Button = $(event.currentTarget),
           Encryption = $Button.attr("data-encryption") || "",
           View = $Button.attr("data-view") || "";
 OH.OpenDialog(View, Encryption);
});
$(document).on("click", ".OpenFirSTEPTool", (event) => {
 const $Button = $(event.currentTarget),
           FirSTEPTool = $Button.attr("data-fst") || "",
           Ground = $Button.attr("data-ground") || "";
 OH.OpenFirSTEPTool(Ground, FirSTEPTool);
});
$(document).on("click", ".PS", (event) => {
 const $Button = $(event.currentTarget),
           Data = $Button.attr("data-type").split(";");
 $.each($(Data[0]).find(Data[1]), function() {
  $(this).hide("slide", {
   direction: "left"
  }, 500);
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
  $(Data[2]).show("slide", {
   direction: "left"
  }, 500);
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
  $(Data[2]).show("slide", {
   direction: "right"
  }, 500);
 }, 600);
});
$(document).on("click", ".Reg", (event) => {
 const $Button = $(event.currentTarget),
  Language = $Button.attr("data-type") || "[App.Language]";
 OH.LocalData("Save", "Language", Language);
 $(".RegSel").fadeOut(500);
});
$(document).on("click", ".RemoveFromAttachments", (event) => {
 const $Button = $(event.currentTarget),
  Input = $(document).find($Button.attr("data-input")),
  ID = $Button.attr("data-id"),
  Value = OH.Base64decrypt($(Input).val());
 if(Value.search(";") > 0) {
  Value = Value.replace(ID + ";", "");
 } else {
  Value = Value.replace(ID, "");
 }
 if(Value === "" || typeof Value === "undefined") {
  $(Input).val(Value);
 } else {
  $(Input).val(OH.Base64encrypt(Value));
 }
});
$(document).on("click", ".ReportContent", (event) => {
 const $Button = $(event.currentTarget),
  ID = $Button.attr("data-id"),
  Processor = OH.Base64decrypt($Button.attr("data-processor")),
  Type = OH.Base64encrypt($Button.attr("data-type"));
 $Button.prop("disabled", true);
 if(ID !== "" && typeof ID !== "undefined") {
  Processor = Processor.replace("[ID]", ID);
  $.ajax({
   error: (error) => {
    OH.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
    Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     OH.Crash(data);
    } else {
     const Data = OH.RenderView(data);
     if(Data.AccessCode === "Accepted") {
      OH.CloseCard();
     }
    }
   },
   url: OH.base + Processor + "&Type=" + Type
  });
 }
});
$(document).on("click", ".SendData", (event) => {
 var $Button = $(event.currentTarget),
       Form = $Button.attr("data-form"),
       FormData = OH.Encrypt($(Form).find(OH.Inputs).serializeArray()) || {},
       Pass = 0,
       Processor = OH.Base64decrypt($Button.attr("data-processor")),
       RequiredInputs = $(Form).find(".req").length,
       Target = $Button.attr("data-target") || Form,
       Text = $Button.text();
 $Button.prop("disabled", true);
 $Button.text("&bull; &bull; &bull;");
 $.each($(Form).find("input[type='email']"), function() {
  $(this).removeClass("Red");
  if(!OH.GetEmailValidation($(this).val())) {
   $(this).addClass("Red");
   OH.Dialog({
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
 }
 if(Pass === 0) {
  $Button.text(Text);
  $Button.prop("disabled", false);
  return;
 } else {
  $.ajax({
   data: FormData,
   error: (error) => {
    OH.Dialog({
     "Body": "Data retrieval error, please see below.",
     "Scrollable": JSON.stringify(error)
    });
   },
   headers: {
    Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
    Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
   },
   method: "POST",
   success: (data) => {
    if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
     OH.Crash(data);
    } else {
     var Class,
      Data = OH.RenderView(data),
      AccessCode = Data.AccessCode,
      Processor = OH.Base64decrypt(Processor),
      Success = Data.Success,
      Type = Data.ResponseType;
     if(!Data.Card && !Data.Dialog && typeof Data.View === "undefined") {
      OH.Dialog({
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
       OH.CloseCard();
      } else if(Success === "CloseDialog") {
       OH.CloseDialog();
      }
      if(Text === "Post") {
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
           $(Parent).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
          }).catch(error => {
           OH.Dialog({
            "Body": "SendData: Error rendering view data. Please see below for more information:",
            "Scrollable": error.message
           });
          });
         }
        }, 600);
       } else if(Type === "ReplaceContent") {
        if(Data.View !== "" && typeof Data.View !== "undefined") {
         Data.View.then(response => {
          $(Target).html(response);
          $(Target).find("input[type=text], textarea").filter(":enabled:visible:first").focus();
         }).catch(error => {
          OH.Dialog({
           "Body": "SendData: Error rendering view data. Please see below for more information:",
           "Scrollable": error.message
          });
         });
        }
       } else if(Type === "UpdateButton") {
        OH.UpdateButton($Button, Data.View);
       } else if(Type === "UpdateText") {
        $Button.text(Data.View);
       }
      }, 750);
      OH.ExecuteCommands(Data.Commands);
     }
     if(Type !== "UpdateButton") {
      $Button.text(Text);
     }
     $Button.prop("disabled", false);
    }
   },
   url: OH.base + Processor
  });
 }
});
$(document).on("click", ".SignOut", (event) => {
 $(event.currentTarget).text("Signing out...");
 OH.SignOut();
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
    $(Element).toggle("slide", {
     direction: "up"
    }, 500);
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
 const Content = OH.DefaultContainer,
  Menu = ".TopBar .MenuContainer";
 if($(".FST").is(":visible")) {
  OH.CloseFirSTEPTool();
 } else {
  if($(".NetMap").is(":visible")) {
   OH.CloseNetMap();
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
 let $Button = $(event.currentTarget);
 if($(".FST").is(":visible")) {
  OH.CloseFirSTEPTool();
 } else {
  if($(".NetMap").is(":visible")) {
   OH.CloseNetMap();
  } else {
   OH.OpenNetMap($Button.attr("data-map"), "AES");
  }
 }
});
$(document).on("click", ".ToggleSideBar", (event) => {
 if($(".FST").is(":visible")) {
  OH.CloseFirSTEPTool();
 } else {
  if($(".NetMap").is(":visible")) {
   OH.CloseNetMap();
  } else {
   $(".SideBar").toggle("slide", {
    direction: "left"
   }, 500);
  }
 }
});
$(document).on("click", ".UpdateButton", (event) => {
 const $Button = $(event.currentTarget),
  Encryption = $Button.attr("data-encryption") || "",
  View = $Button.attr("data-processor") || "";
 if(Encryption === "AES") {
  View = OH.AESdecrypt(View);
 } else {
  View = OH.Base64decrypt(View);
 }
 $Button.prop("disabled", true);
 $Button.text(OH.Loading);
 $.ajax({
  error: (error) => {
   OH.Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
   Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    OH.Crash(data);
   } else {
    const Data = OH.RenderView(data);
    if(Data.View !== "" && typeof Data.View !== "undefined") {
     Data.View.then(response => {
      OH.UpdateButton($Button, response);
      $Button.prop("disabled", false);
      OH.ExecuteCommands(Data.Commands);
     }).catch(error => {
      OH.Dialog({
       "Body": "UpdateButton: Error rendering view data. Please see below:",
       "Scrollable": error.message
      });
     });
    }
   }
  },
  url: OH.base + View
 });
});
$(document).on("click", ".UpdateContent", (event) => {
 const $Button = $(event.currentTarget),
  Container = $Button.attr("data-container") || OH.DefaultContainer,
  Encryption = $Button.attr("data-encryption") || "",
  View = $Button.attr("data-view") || "";
 $Button.prop("disabled", true);
 setTimeout(() => {
  OH.UpdateContent(Container, View, Encryption);
 }, 500);
});
$(document).on("click", ".UploadFiles", (event) => {
 const $Button = $(event.currentTarget);
 event.preventDefault();
 $Button.prop("disabled", true);
 $Button.text(OH.Loading);
 OH.Upload($Button);
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
    OH.Dialog({
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
  Data = OH.Base64decrypt($Input.attr("data-u")),
  F = $Input.closest(".DiscountCodesCC");
 Data = Data.replace("[DC]", OH.Base64encrypt($Input.val()));
 Data = Data.replace("[ID]", OH.Base64encrypt($Input.attr("data-id")));
 $.ajax({
  error: (error) => {
   OH.Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
   Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    OH.Crash(data);
   } else {
    var data = OH.Base64decrypt(data);
    if(data[0] === "Accepted") {
     $(F).html(data[1]);
    } else {
     $(F).find("p.c:last").html(data[1]);
    }
   }
  },
  url: OH.base + Data
 });
});
$(document).on("keyup", ".LinkData", (event) => {
 const $Input = $(event.currentTarget),
  Link = OH.Base64encrypt($Input.val()),
  Preview = OH.Base64decrypt($Input.attr("data-preview"));
 $.ajax({
  error: (error) => {
   OH.Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
   Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    OH.Crash(data);
   } else {
    const Data = OH.RenderView(data);
    if(Data.View !== "" && typeof Data.View !== "undefined") {
     Data.View.then(response => {
      $(".AddLink > .LinkPreview").html(response);
      OH.ExecuteCommands(Data.Commands);
     }).catch(error => {
      OH.Dialog({
       "Body": "LinkData: Error rendering view data. Please see below:",
       "Scrollable": error.message
      });
     });
    }
   }
  },
  url: OH.base + Preview + "&Link=" + Link
 });
});
$(document).on("keyup", ".ReSearch", (event) => {
 const $Input = $(event.currentTarget);
 OH.ReSearch($Input);
});
$(document).on("keyup", ".SearchBar", (event) => {
 const $Input = $(event.currentTarget);
 $(".SideBar").hide("slide", {direction: "left"}, 500);
 OH.CloseFirSTEPTool();
 OH.CloseNetMap();
 OH.UpdateContent(OH.DefaultContainer, OH.AESencrypt(OH.AESdecrypt($Input.attr("data-u"))) + OH.AESencrypt($Input.val()), "AES");
});
$(document).on("keyup", ".UnlockProtectedContent", (event) => {
 const $Input = $(event.currentTarget),
           Key = OH.Base64encrypt($Input.val()),
           Parent = $Input.closest(".ProtectedContent"),
           SignOut = $Input.attr("data-signout") || "";
 $.ajax({
  error: (error) => {
   OH.Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  headers: {
   Language: OH.AESencrypt(OH.LocalData("Get", "Language")),
   Token: OH.AESencrypt(OH.LocalData("Get", "SecurityKey"))
  },
  method: "POST",
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    OH.Crash(data);
   } else {
    const Data = OH.RenderView(data);
    if(Data.View !== "" && typeof Data.View !== "undefined") {
     Data.View.then(response => {
      $Input.prop("disabled", true);
      setTimeout(() => {
       if(SignOut === "Yes") {
        OH.InstantSignOut();
       }
       $(Parent).empty();
       if(Data.AddTopMargin === 1) {
        $(Parent).append("<div class='TopBarMargin'></div>\r\n");
       }
       $(Parent).append(response);
       OH.ExecuteCommands(Data.Commands);
      }, 600);
     }).catch(error => {
      OH.Dialog({
       "Body": "UnlockProtectedContent: Error rendering view data. Please see below for more information:",
       "Scrollable": error.message
      });
     });
    }
   }
  },
  url: OH.base + OH.Base64decrypt($Input.attr("data-view")) + "&Key=" + Key
 });
});
$(() => {
 $.ajax({
  error: (error) => {
   OH.Dialog({
    "Body": "Data retrieval error, please see below.",
    "Scrollable": JSON.stringify(error)
   });
  },
  success: (data) => {
   if(/<\/?[a-z][\s\S]*>/i.test(data) === true) {
    OH.Crash(data);
   } else {
    var data = JSON.parse(OH.AESdecrypt(data));
    OH.SaveToDatabase("Extensions", data.JSON).then(() => {
     OH.UpdateContent(".RegSel", "[App.SwitchLanguages]", "AES");
     if($(location).attr("href") === "[App.Base]/") {
      setTimeout(() => {
       OH.SetUIVariant(OH.DefaultUI);
       $(".Boot").fadeOut(500);
      }, 1000);
     } else {
      $(".Boot").fadeOut(500);
     }
    }).catch(error => {
     OH.Dialog({
      "Body": "Unable to complete the boot process. See below for more information:",
      "Scrollable": error.message
     });
    });
   }
  },
  url: "[App.Base]/extensions"
 });
 setInterval(() => {
  OH.Language = OH.LocalData("Get", "Language");
  if(OH.Language === "" || typeof OH.Language === "undefined") {
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