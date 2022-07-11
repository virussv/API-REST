axios.defaults.baseURL = "http://localhost/APIS/API-REST/backend/public";
const form = document.querySelector("#form");
const form2 = document.querySelector("#form2");
const auth = document.querySelector("#auth"); 

form.addEventListener("submit",async (event)=>{
    event.preventDefault();

    try {
        const formData = new FormData(event.target);
        const {data} = await axios.post("login/",formData);

        sessionStorage.setItem("refreshToken",data.refreshToken);
        sessionStorage.setItem("token",data.token);
        sessionStorage.setItem("name",data.name);


        console.log(data)
    } catch (error){
        console.log("error")
    }
})

auth.addEventListener("click",async ()=>{
    try {
        const {data} = await axios.post("auth/",null,{
            headers:{
                "Authorization": "Bearer:" + sessionStorage.getItem("name") + ", refreshToken:" + sessionStorage.getItem("refreshToken") + ",token:" + sessionStorage.getItem("token")
            }
        })

        console.log(data)
        
    } catch (error) {
        console.log("error");
    }
})

form2.addEventListener("submit",async (event)=>{
    event.preventDefault();

    try {
        const formData = new FormData(event.target)

        const {data} = await axios.post("post/",formData,{
            headers:{
                "Authorization": "Bearer:" + sessionStorage.getItem("name") + ", refreshToken:" + sessionStorage.getItem("refreshToken") + ",token: " + sessionStorage.getItem("token")
            }
        })

        const token = data['0']['newToken'] ?? null;
        if(token != undefined)
        {
            sessionStorage.setItem("token",token)
        }

        let containermsg = document.getElementById("container-msg")
        let pmsg = document.getElementById("p-msg")
        let inputs = document.querySelectorAll(".input-cadastro")

        if(data['1']['message'] === "Preencha todos os campos")
        {
            containermsg.style.backgroundColor = "#d32d2d"
            pmsg.textContent = data['1']['message']
        }

        if(data['1']['message'] === "Este email já esta sendo usado!")
        {
            containermsg.style.backgroundColor = "#d32d2d"
            pmsg.textContent = data['1']['message']
        }

        if(data['1']['message'] === "O usuario foi inserido com sucesso")
        for(let i = 0;i < inputs.length;i++)
        {
            containermsg.style.backgroundColor = "rgb(58 155 39)"
            pmsg.textContent = data['1']['message']
            inputs[i].value = ""
        }

        console.log(data)
   
    } catch (err) {
        console.log("ERROR")
    }
 
})


