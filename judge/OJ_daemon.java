import java.io.*;
import java.net.ServerSocket;
import java.net.Socket;

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
			dir = new File("submitted_codes/"+submission_id);
			dir.mkdirs();

			String filename = in.readLine();
			System.out.println("file name is " + filename);
			String code = in.readLine().replace("$_\\_$", "\n");
			System.out.println("code follows \n"+code);
			int timeout = (int) Long.parseLong(in.readLine()); //TODO: make it float
			System.out.println("timeout i " + timeout);
			String lang = in.readLine();
			System.out.println("language is " + lang);
			int total_testcases=Integer.parseInt(in.readLine());
			System.out.println("total testcases are  " + total_testcases);
			for(int i=1;i<=total_testcases;i++){
				String test_in = in.readLine().replace("$_\\_$", "\n");
				System.out.println("test_in \n" + test_in);
				String test_out = in.readLine().replace("$_\\_$", "\n");
				System.out.println("test_out is \n" + test_out);
			}
			//String test_in = in.readLine().replace("$_\\_$", "\n");
			/*System.out.println("Compiling " + file + "...");
			// create the sample	 input file
			PrintWriter writer = new PrintWriter(new FileOutputStream("submitted_codes/"+submission_id +"/in.txt"));
			writer.println(input);
			writer.close();
			Language l = null;
			// create the language specific compiler
			if(lang.equals("c"))
				l = new C(file, timeout, contents, dir.getAbsolutePath());
			else if(lang.equals("cpp"))
				l = new Cpp(file, timeout, contents, dir.getAbsolutePath());
			else if(lang.equals("java"))
				l = new Java(file, timeout, contents, dir.getAbsolutePath());
			else if(lang.equals("python"))
				l = new Python(file, timeout, contents, dir.getAbsolutePath());
			l.compile(); // compile the file
			String errors = compileErrors();
			if(!errors.equals("")) { // check for compilation errors
				out.println("0");
				out.println(errors);
			} else {
				// execute the program and return output
				l.execute();
				if(l.timedout)
					out.println(2);
				else {
					out.println("1");
					out.println(execMsg());
				}
			*/
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
