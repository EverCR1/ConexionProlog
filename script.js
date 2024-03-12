document.addEventListener("DOMContentLoaded", function() {

    const chatbotToggler = document.querySelector(".chatbot-toggler");
    const closeBtn = document.querySelector(".close-btn");
    const chatbox = document.querySelector(".chatbox");
    const chatInput = document.querySelector(".chat-input textarea");
    const sendChatBtn = document.querySelector(".chat-input span");

    const createChatLi = (message, className) => {
        const chatLi = document.createElement("li");
        chatLi.classList.add("chat", `${className}`);
        let chatContent = className === "outgoing" ? `<p></p>` : `<span class="material-symbols-outlined">smart_toy</span><p></p>`;
        chatLi.innerHTML = chatContent;
        chatLi.querySelector("p").textContent = message;
        return chatLi;
    }

    const generateResponse = (userMessage) => {
        fetch("chat.php", {
            method: "POST",
            body: JSON.stringify({ message: userMessage }),
            headers: {
                "Content-Type": "application/json"
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            const responseLi = createChatLi(data, "incoming");
            chatbox.appendChild(responseLi);
            chatbox.scrollTo(0, chatbox.scrollHeight);
        })
        .catch(error => {
            console.error("Error:", error);
            const errorMessage = "Oops! Algo salió mal. Por favor, intenta de nuevo.";
            const errorLi = createChatLi(errorMessage, "incoming");
            errorLi.classList.add("error");
            chatbox.appendChild(errorLi);
            chatbox.scrollTo(0, chatbox.scrollHeight);
        });
    }

    const handleChat = () => {
        const userMessage = chatInput.value.trim();
        if (!userMessage) return;

        chatInput.value = "";
        const outgoingChatLi = createChatLi(userMessage, "outgoing");
        chatbox.appendChild(outgoingChatLi);
        chatbox.scrollTo(0, chatbox.scrollHeight);
        
        const thinkingMessage = "Pensando...";
        const thinkingLi = createChatLi(thinkingMessage, "incoming");
        chatbox.appendChild(thinkingLi);
        chatbox.scrollTo(0, chatbox.scrollHeight);

        generateResponse(userMessage);
    }

    chatInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey && window.innerWidth > 800) {
            e.preventDefault();
            handleChat();
        }
    });

    sendChatBtn.addEventListener("click", handleChat);
    closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
    chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));

});