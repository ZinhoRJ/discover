<style>
.banner-container {
  position: relative;
  width: 100%;
  height: 400px;
  margin: auto;
  /*overflow: hidden;
  background: #f0f0f0;*/
  background: -webkit-linear-gradient(45deg, #A7D3F2, #0D47A1);
  font-family: sans-serif;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);

  margin-bottom: 80px;
  background: url("../svg/60-lines.png");
  transition: 0.5s ease;
}

.banner {
  display: flex;
  position: relative;
  width: 100%;
  height: 100%;
  transition: all 0.5s ease-in-out;

  border-radius: 10px;

  overflow: hidden;
}

.top-button {
  display: block;
  position: absolute;
  top: 325px;
  left: 20px;
  padding: 8px 12px;
  
  font-size: 1rem;
  background-color: rgba(255, 255, 255, 0.13);
  border-radius: 5px;
  border: 2px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0 40px rgba(28, 27, 29, 0.199);

  color: whitesmoke;
  cursor: pointer;

  
  height: 50px;
  width: 45%;
}
.top-button:hover {
  background-color: rgba(255, 255, 255, 0.2);
  box-shadow: 0 0 50px rgba(28, 27, 29, 0.3);
  transition: background-color 0.3s, box-shadow 0.3s;
}

.btnEsquerda {
  display: block;
  width: 100%;
  margin-bottom: 0.5rem;
  padding: 0.6rem;
  font-size: 1rem;
  cursor: pointer;

  background-color: rgba(255, 255, 255, 0.13);
  border-radius: 5px;
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0 40px rgba(28, 27, 29, 0.199);
  color: whitesmoke;
}
.btnEsquerda:hover {
  background-color: rgba(255, 255, 255, 0.2);
  box-shadow: 0 0 50px rgba(28, 27, 29, 0.3);
  transition: background-color 0.3s, box-shadow 0.3s;
}

.text-content {
  position: absolute;
  bottom: 80px;
  left: 20px;
  color: #333;
  max-width: 45%;
}

.image-area {
  margin-left: auto;
  width: 50%;
  height: 100%;
}

.image-area img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  overflow: hidden;
  border-radius: 0 10px 10px 0;
}

.controls {
  display: flex;
  position: absolute;
  justify-content: space-between;
  margin-top: 10px;
  width: 100%;
}

.controls button {
  padding: 10px;
  font-size: 18px;
  cursor: pointer;
}

.indicators {
  text-align: center;
  margin-top: auto;
  margin-bottom: auto;
}

.dot {
  height: 12px;
  width: 12px;
  margin: 0 5px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
}

.dot.active {
  background-color: #333;
}

</style>

<div class="banner-container">
  <div class="banner">
    <div class="text-content">
      <h2 class="title">Título do Slide</h2>
      <p class="description">Descrição breve do conteúdo aqui.</p>
    </div>
    <button class="top-button">Menu</button>
    <div class="image-area">
      <img src="img1.jpg" alt="Imagem do Slide">
    </div>
  </div>

  <!-- Botões externos -->
  <div class="controls">
    <button onclick="prevSlide()">◀</button>
    
    <!-- Indicadores -->
    <div class="indicators">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    </div>

    <button onclick="nextSlide()">▶</button>
  </div>
</div>

<script>
    const slides = [
  {
    title: "Seja Descoberto!",
    description: "Envie sua música e seja reconhecido pela comunidade do Discover.",
    image: "./svg/logo-banner.png",
    background: "-webkit-linear-gradient(90deg, #41295a, #2F0743)",
    color: "whitesmoke",
    //width: "550px",
    //height: "400px",
    btnText: "Enviar Música",
    btnLink: "./formulario.php"
  },
  {
    title: "Segundo Slide",
    description: "Descrição do segundo slide.",
    image: "./uploads/padrao.jpg",
    background: "-webkit-linear-gradient(45deg, #000000ff, #0D47A1)",
    color: "whitesmoke",
    btnText: "botão",
    btnLink: "saiba-mais.php"
  },
  {
    title: "Terceiro Slide",
    description: "Descrição do terceiro slide.",
    image: "./uploads/padrao.jpg",
    background: "-webkit-linear-gradient(45deg, #A7D3F2, #0D47A1)",
    color: "black",
    btnText: "botão",
    btnLink: "contato.php",
    btnColor: "black"
  }
];

let current = 0;

function updateSlide() {
  const banner = document.querySelector(".banner");
  const title = document.querySelector(".title");
  const desc = document.querySelector(".description");
  const img = document.querySelector(".image-area img");
  const dots = document.querySelectorAll(".dot");
  const button = document.querySelector(".top-button");

  title.textContent = slides[current].title;
  desc.textContent = slides[current].description;
  img.src = slides[current].image;
  img.style.height = slides[current].height || "100%";
  img.style.width = slides[current].width || "100%";
  banner.style.background = slides[current].background;
  title.style.color = slides[current].color;
  desc.style.color = slides[current].color;
  button.style.color = slides[current].btnColor || "whitesmoke";

  button.textContent = slides[current].btnText;
  button.onclick = () => {
    const iframe = parent.document.querySelector("#main");
    if (iframe) {
      iframe.src = slides[current].btnLink;
    }
  };

  dots.forEach(dot => dot.classList.remove("active"));
  dots[current].classList.add("active");
}

function nextSlide() {
  current = (current + 1) % slides.length;
  updateSlide();
}

function prevSlide() {
  current = (current - 1 + slides.length) % slides.length;
  updateSlide();
}

setInterval(nextSlide, 5000); // muda a cada 5 segundos
updateSlide(); // inicializa

</script>