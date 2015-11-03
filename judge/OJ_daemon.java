import java.io.*;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.*;
class OJ_daemon {
	public static void main(String args[]) {
		try {
			ServerSocket server = new ServerSocket(4001); // create a new socket to listen on
			System.out.println("OJ_daemon started...");
			while(true) {
				//n++;
				// accept any incoming connection and process it on a new thread
				Socket s = server.accept();
				//RequestThread request = new RequestThread(s, n);
				//request.start();
			
		
	

				File dir; // staging directory
	
	/*public RequestThread(Socket s, int n) {
		this.s=s;
		this.n=n;
		dir = new File("stage/" + n);
	}
	
		dir.mkdirs(); // create staging directory*/
		try {
			
			BufferedReader in = new BufferedReader(new InputStreamReader(s.getInputStream()));
			PrintWriter out = new PrintWriter(s.getOutputStream(), true);
			//System.out.println("aaha" + in.readLine());
			// read input from the PHP script
			String submission_id = in.readLine();
			String submission_dir = in.readLine();
			String filename = in.readLine();
			System.out.println("file name is " + filename);
			String compile_script_dir = in.readLine();
			PrintWriter code_writer = new PrintWriter(submission_dir+"/"+filename, "UTF-8");
			System.out.println("location of file " + submission_dir+"/"+filename);
			in.readLine();
			String temp="";
			String code="";
			while(!((temp=in.readLine()).equals("\\end\\"))){
				code=code+temp+"\n";				
			}
			
			//code = code.replace("$_t_$","\t");
			code_writer.print(code);
			//System.out.println("code loaded:"+code);
			code_writer.flush();
			code_writer.close();
			System.out.println("code follows \n"+code);
			int timeout = (int) Long.parseLong(in.readLine()); //TODO: make it float
			//System.out.println("timeout i " + timeout);
			String lang = in.readLine();
			ProcessBuilder bd = new ProcessBuilder("sh","java_compile.sh",submission_dir,filename); //TODO:compile script change
       			bd.directory(new File(compile_script_dir));
			Process proc = bd.start();
			OutputStream stdin = proc.getOutputStream();
        		InputStream stdout = proc.getInputStream();
			BufferedReader reader = new BufferedReader(new InputStreamReader(stdout));
       		 	BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(stdin));
			
			int codeExitValue=0;
			try{
				codeExitValue = proc.waitFor();
			}
			catch(Exception e){
				e.printStackTrace();
			}
			if(codeExitValue!=0){
				String str = "";
				while ((str=reader.readLine())!=null) {
            				out.println(str);
        			}
			}
			else{
			
			//System.out.println("language is " + lang);
			int total_testcases=Integer.parseInt(in.readLine());
			//System.out.println("total testcases are  " + total_testcases);
			for(int i=1;i<=total_testcases;i++){
				String test_in = in.readLine().replace("$_n_$", "\n");
				System.out.println("test_in \n" + test_in);
				String test_out = in.readLine().replace("$_n_$", "\n");
				System.out.println("test_out is \n" + test_out);

				ProcessBuilder builder = new ProcessBuilder("sh","java_run.sh",submission_dir,filename);
        			builder.directory(new File(compile_script_dir));
       				Process process = builder.start();
       				stdin = process.getOutputStream();
        			stdout = process.getInputStream();
				reader = new BufferedReader(new InputStreamReader(stdout));
       		 		writer = new BufferedWriter(new OutputStreamWriter(stdin));

				writer.write(test_in);
        			writer.flush();
        			writer.close();
				String str="";
				out.println("result follows");
				String code_out="";
        		while ((str=reader.readLine())!=null) {
            			code_out+=str+"\n";
        		}
			out.println(code_out);
			code_out=code_out.trim();
			test_out=test_out.trim();
			int runExitCode=0;
			try{
			runExitCode = process.waitFor();
			}
			catch(Exception e){
				e.printStackTrace();	
			}
			
			if(runExitCode!=0){
				out.println("runtime error");			
			}
			else{
			if(code_out.equals(test_out)){
				out.println("success");			
			}
			else{
				out.println("code_out:"+code_out);
				out.println("test_out:"+test_out);
				out.println("wrong ans");
			}
			}	
			}
			}
			out.println("alright");
			
			s.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
		} catch (IOException e) {
			e.printStackTrace();
		}
		try{Thread.sleep(1000);}
		catch(Exception e){
			e.printStackTrace();
		}
	}
	// method to return the compiler errors
	/*public String compileErrors() {
		String line, content = "";
		try {
			BufferedReader fin = new BufferedReader(new InputStreamReader(new FileInputStream(dir.getAbsolutePath() + "/err.txt")));
			while((line = fin.readLine()) != null)
				content += (line + "\n");
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return content.trim();
	}
	
	// method to return the execution output
	public String execMsg() {
		String line, content = "";
		try {
			BufferedReader fin = new BufferedReader(new InputStreamReader(new FileInputStream(dir.getAbsolutePath() + "/out.txt")));
			while((line = fin.readLine()) != null)
				content += (line + "\n");
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return content.trim();
	}*/
}
