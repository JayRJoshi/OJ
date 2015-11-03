var c_text="#include <stdio.h>\n\
#include <string.h>\n\
#include <math.h>\n\
#include <stdlib.h>\n\n\
int main() {\n\n\
	/* Enter your code here. Read input from STDIN. Print output to STDOUT... */\n\
	return 0;\n\
}";	

var cpp_text="#include <cmath>\n\
#include <cstdio>\n\
#include <vector>\n\
#include <iostream>\n\
#include <algorithm>\n\
using namespace std;\n\
\n\
int main() {\n\
	int n;\n\
	int sum;\n\
 	cin>>n;\n\
	/* code here */\n\
 	cout<<sum;\n\
  	return 0;\n\
}"

var java_text="import java.io.*;\n\
import java.util.*;\n\
import java.text.*;\n\
import java.math.*;\n\
import java.util.regex.*;\n\n\
class Main {\n\n\
\
	public static void main(String[] args) {\n\
		/* Enter your code here. Read input from STDIN. Print output to STDOUT. Your class should be named Solution. */\n\
	}\n\
}";

function lang_select(lang,lang_selected){
	document.getElementById(lang_selected).innerHTML=lang.innerHTML;
	switch(lang.innerHTML) {
    	case 'c':
 		editor.setOption("mode","text/x-csrc");
		
		editor.getDoc().setValue(c_text);
        break;
    	case 'c++':
        	editor.setOption("mode","text/x-c++src");
		
	editor.getDoc().setValue(cpp_text);
        break;
	case 'java':
		editor.setOption("mode","text/x-java");
		
		editor.getDoc().setValue(java_text);
	break;
    	default:
	}
}
function lang_select(lang,lang_selected,lang_id){
	document.getElementById(lang_selected).innerHTML=lang.innerHTML;
	document.getElementById(lang_id).value=lang.innerHTML;
	switch(lang.innerHTML) {
    	case 'c':
 		editor.setOption("mode","text/x-csrc");
		
		editor.getDoc().setValue(c_text);
        break;
    	case 'c++':
        	editor.setOption("mode","text/x-c++src");
		
	editor.getDoc().setValue(cpp_text);
        break;
	case 'java':
		editor.setOption("mode","text/x-java");
		
		editor.getDoc().setValue(java_text)s;
	break;
    	default:
	}
}

function file_to_editor(file_id) {
    	fileInput=document.getElementById(file_id);
    	fileInput.addEventListener('change', function (e) {
        var file = fileInput.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
        editor.getDoc().setValue(reader.result);
        };
        reader.readAsText(file);
        
    });
};

codearea.value = cm.getValue();
