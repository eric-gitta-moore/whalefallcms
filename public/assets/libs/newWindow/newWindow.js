window.confirm = function (message) {    
	try {    
		var iframe = document.createElement("IFRAME");    
		iframe.style.display = "none";    
		iframe.setAttribute("src", 'data:text/plain,');    
		document.documentElement.appendChild(iframe);    
		var alertFrame = window.frames[0];    
		var iwindow = alertFrame.window;    
		if (iwindow == undefined) {    
			iwindow = alertFrame.contentWindow;    
		}    
		var result=iwindow.confirm(message);    
		iframe.parentNode.removeChild(iframe);    
		return result;  
	}    
	catch (exc) {    
		return wConfirm(message);    
	}    
};
window.alert = function (message) {    
    try {    
        var iframe = document.createElement("IFRAME");    
        iframe.style.display = "none";    
        iframe.setAttribute("src", 'data:text/plain,');    
        document.documentElement.appendChild(iframe);    
        var alertFrame = window.frames[0];    
        var iwindow = alertFrame.window;    
        if (iwindow == undefined) {    
            iwindow = alertFrame.contentWindow;    
        }    
        iwindow.alert(message);    
        iframe.parentNode.removeChild(iframe);    
    }    
    catch (exc) {    
        return wAlert(message);    
    }    
}  ;