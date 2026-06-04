<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>SHOP HOLY </title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Montserrat:wght@300;400;600;700&family=Cormorant+Garamond:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/8f3b7d3f19.js" crossorigin="anonymous"></script>
<meta name="viewport" content="width=device-width,initial-scale=1">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Montserrat',sans-serif;
  background:#0a0a0a;
  color:#f5f5f0;
  overflow-x:hidden;
}

.hero{
  height:100vh;
  position:relative;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
}

.hero-bg{
  position:absolute;
  inset:0;
  background:
    radial-gradient(circle at 20% 50%, rgba(194,161,104,0.15), transparent 50%),
    radial-gradient(circle at 80% 50%, rgba(139,119,85,0.12), transparent 50%),
    linear-gradient(135deg,rgba(10,10,10,0.95),rgba(20,20,20,0.98)),
    url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1920') center/cover;
  filter:brightness(0.4) contrast(1.1);
  animation:subtleZoom 20s ease infinite alternate;
}
@keyframes subtleZoom{
  from{transform:scale(1);}
  to{transform:scale(1.05);}
}

.hero-overlay{
  position:absolute;
  inset:0;
  background:linear-gradient(180deg, rgba(10,10,10,0.3) 0%, rgba(10,10,10,0.8) 100%);
}

.hero-content{
  position:relative;
  z-index:10;
  text-align:center;
  padding:20px;
}

.brand-logo{
  font-family:'Playfair Display',serif;
  font-size:clamp(70px,13vw,160px);
  font-weight:900;
  letter-spacing:18px;
  background:linear-gradient(135deg,#d4af37 0%,#f4e4c1 25%,#c9a961 50%,#f4e4c1 75%,#d4af37 100%);
  background-size:300%;
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  animation:shimmer 8s ease infinite;
  text-shadow:0 0 80px rgba(212,175,55,0.3);
  margin-bottom:20px;
  position:relative;
}

.brand-logo::before{
  content:'SHOP HOLY';
  position:absolute;
  top:0;
  left:0;
  right:0;
  background:linear-gradient(135deg,#d4af37,#f4e4c1);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  filter:blur(20px);
  opacity:0.5;
  z-index:-1;
}

@keyframes shimmer{
  0%,100%{background-position:0% 50%;}
  50%{background-position:100% 50%;}
}

.tagline{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(20px,3.5vw,32px);
  font-weight:300;
  letter-spacing:8px;
  color:#c9a961;
  margin-bottom:50px;
  text-transform:uppercase;
  font-style:italic;
}

.cta-buttons{
  display:flex;
  gap:25px;
  justify-content:center;
  flex-wrap:wrap;
  margin-top:60px;
}

.btn{
  padding:18px 50px;
  font-size:14px;
  font-weight:600;
  text-transform:uppercase;
  letter-spacing:3px;
  border:none;
  cursor:pointer;
  transition:all 0.4s cubic-bezier(0.4,0,0.2,1);
  position:relative;
  overflow:hidden;
  text-decoration:none;
  display:inline-block;
  font-family:'Montserrat',sans-serif;
}

.btn::before{
  content:'';
  position:absolute;
  top:50%;
  left:50%;
  width:0;
  height:0;
  border-radius:50%;
  background:rgba(255,255,255,0.1);
  transform:translate(-50%,-50%);
  transition:width 0.6s,height 0.6s;
}

.btn:hover::before{
  width:400px;
  height:400px;
}

.btn-primary{
  background:linear-gradient(135deg,#d4af37,#c9a961);
  color:#0a0a0a;
  box-shadow:0 10px 40px rgba(212,175,55,0.3);
  position:relative;
}
.btn-primary:hover{
  transform:translateY(-3px);
  box-shadow:0 15px 50px rgba(212,175,55,0.5);
  background:linear-gradient(135deg,#f4e4c1,#d4af37);
}

.btn-secondary{
  background:rgba(201,169,97,0.05);
  color:#d4af37;
  border:2px solid #c9a961;
  box-shadow:0 0 30px rgba(201,169,97,0.2);
  backdrop-filter:blur(10px);
}
.btn-secondary:hover{
  background:rgba(201,169,97,0.15);
  border-color:#d4af37;
  box-shadow:0 0 40px rgba(212,175,55,0.4);
  transform:translateY(-3px);
}

.scroll-indicator{
  position:absolute;
  bottom:40px;
  left:50%;
  transform:translateX(-50%);
  font-size:12px;
  color:#c9a961;
  text-align:center;
  animation:float 3s ease infinite;
  letter-spacing:2px;
}
@keyframes float{
  0%,100%{transform:translateX(-50%) translateY(0);}
  50%{transform:translateX(-50%) translateY(-15px);}
}

/*  SEGUNDAS SECCIONES */
.about{
  padding:120px 20px;
  background:
    radial-gradient(circle at 10% 20%, rgba(194,161,104,0.08), transparent 40%),
    radial-gradient(circle at 90% 80%, rgba(139,119,85,0.06), transparent 40%),
    linear-gradient(180deg,#0a0a0a,#111111);
  text-align:center;
  position:relative;
}

.about::before{
  content:'';
  position:absolute;
  top:0;
  left:0;
  right:0;
  height:1px;
  background:linear-gradient(90deg,transparent,#c9a961,transparent);
}

.about h2{
  font-family:'Playfair Display',serif;
  font-size:clamp(45px,7vw,75px);
  color:#d4af37;
  margin-bottom:40px;
  letter-spacing:6px;
  font-weight:700;
  position:relative;
  display:inline-block;
}

.about h2::after{
  content:'';
  position:absolute;
  bottom:-15px;
  left:50%;
  transform:translateX(-50%);
  width:80px;
  height:2px;
  background:linear-gradient(90deg,transparent,#d4af37,transparent);
}

.about p{
  max-width:750px;
  margin:0 auto 70px;
  font-size:19px;
  line-height:2;
  color:#d0cfc5;
  font-weight:300;
  letter-spacing:0.5px;
}

.features{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
  gap:40px;
  max-width:1200px;
  margin:0 auto;
  padding:0 20px;
}

.feature-card{
  background:linear-gradient(135deg,rgba(201,169,97,0.05),rgba(139,119,85,0.03));
  border:1px solid rgba(201,169,97,0.2);
  padding:50px 35px;
  border-radius:8px;
  transition:all 0.5s cubic-bezier(0.4,0,0.2,1);
  cursor:pointer;
  position:relative;
  overflow:hidden;
  backdrop-filter:blur(10px);
}

.feature-card::before{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(135deg,rgba(212,175,55,0.1),transparent);
  opacity:0;
  transition:opacity 0.5s;
}

.feature-card::after{
  content:'';
  position:absolute;
  top:0;
  left:-100%;
  width:100%;
  height:100%;
  background:linear-gradient(90deg,transparent,rgba(212,175,55,0.1),transparent);
  transition:left 0.6s;
}

.feature-card:hover::after{
  left:100%;
}

.feature-card:hover::before{opacity:1;}
.feature-card:hover{
  transform:translateY(-8px);
  box-shadow:0 20px 60px rgba(212,175,55,0.2);
  border-color:rgba(212,175,55,0.5);
}

.feature-icon{
  font-size:48px;
  margin-bottom:25px;
  background:linear-gradient(135deg,#d4af37,#f4e4c1);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  filter:drop-shadow(0 0 20px rgba(212,175,55,0.3));
}

.feature-card h3{
  font-family:'Playfair Display',serif;
  font-size:26px;
  margin-bottom:18px;
  color:#f5f5f0;
  letter-spacing:2px;
  font-weight:600;
}

.feature-card p{
  color:#b8b5a8;
  font-size:15px;
  line-height:1.8;
  font-weight:300;
  letter-spacing:0.5px;
}

/* COLECCIONES */
.collections{
  padding:120px 20px;
  background:#0a0a0a;
  position:relative;
}

.collections::before{
  content:'';
  position:absolute;
  top:0;
  left:0;
  right:0;
  height:1px;
  background:linear-gradient(90deg,transparent,#c9a961,transparent);
}

.collections h2{
  font-family:'Playfair Display',serif;
  font-size:clamp(45px,7vw,75px);
  color:#d4af37;
  text-align:center;
  margin-bottom:80px;
  letter-spacing:6px;
  font-weight:700;
  position:relative;
  display:inline-block;
  left:50%;
  transform:translateX(-50%);
}

.collections h2::after{
  content:'';
  position:absolute;
  bottom:-15px;
  left:50%;
  transform:translateX(-50%);
  width:80px;
  height:2px;
  background:linear-gradient(90deg,transparent,#d4af37,transparent);
}

.collection-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
  gap:30px;
  max-width:1200px;
  margin:0 auto;
}

.collection-item{
  position:relative;
  height:450px;
  overflow:hidden;
  border-radius:4px;
  cursor:pointer;
  border:1px solid rgba(201,169,97,0.15);
  transition:border-color 0.4s;
}

.collection-item:hover{
  border-color:rgba(212,175,55,0.5);
}

.collection-item img{
  width:100%;
  height:100%;
  object-fit:cover;
  transition:transform 0.7s cubic-bezier(0.4,0,0.2,1);
  filter:brightness(0.7) contrast(1.1);
}

.collection-item:hover img{
  transform:scale(1.1);
  filter:brightness(0.85) contrast(1.15);
}

.collection-overlay{
  position:absolute;
  inset:0;
  background:linear-gradient(180deg,transparent 30%,rgba(10,10,10,0.7) 70%,rgba(10,10,10,0.95) 100%);
  display:flex;
  align-items:flex-end;
  padding:40px;
  opacity:0;
  transition:opacity 0.5s;
}

.collection-item:hover .collection-overlay{
  opacity:1;
}

.collection-overlay h3{
  font-family:'Playfair Display',serif;
  font-size:34px;
  color:#d4af37;
  letter-spacing:4px;
  font-weight:600;
  text-shadow:0 2px 20px rgba(0,0,0,0.5);
}

/* FOOTER */
.footer{
  padding:80px 20px;
  background:linear-gradient(180deg,#0a0a0a,#000000);
  text-align:center;
  border-top:1px solid rgba(201,169,97,0.2);
  position:relative;
}

.footer-content{
  max-width:1200px;
  margin:0 auto;
}

.footer h3{
  font-family:'Playfair Display',serif;
  font-size:42px;
  color:#d4af37;
  margin-bottom:20px;
  letter-spacing:8px;
  font-weight:700;
}

.footer > p:first-of-type{
  color:#b8b5a8;
  font-size:15px;
  letter-spacing:2px;
  margin-bottom:10px;
}

.social-links{
  display:flex;
  gap:20px;
  justify-content:center;
  margin:40px 0;
}

.social-links a{
  width:55px;
  height:55px;
  border-radius:50%;
  background:rgba(201,169,97,0.08);
  border:1px solid rgba(201,169,97,0.3);
  display:flex;
  align-items:center;
  justify-content:center;
  color:#d4af37;
  font-size:20px;
  transition:all 0.4s cubic-bezier(0.4,0,0.2,1);
  text-decoration:none;
  position:relative;
  overflow:hidden;
}

.social-links a::before{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(135deg,#d4af37,#c9a961);
  opacity:0;
  transition:opacity 0.4s;
  border-radius:50%;
}

.social-links a:hover::before{
  opacity:1;
}

.social-links a i{
  position:relative;
  z-index:1;
  transition:color 0.4s;
}

.social-links a:hover{
  transform:translateY(-5px);
  box-shadow:0 10px 30px rgba(212,175,55,0.3);
  border-color:transparent;
}

.social-links a:hover i{
  color:#0a0a0a;
}

.footer p:last-child{
  color:#6a6a6a;
  font-size:13px;
  margin-top:40px;
  letter-spacing:1px;
}

/* POPUP */
.popup{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.92);
  backdrop-filter:blur(15px);
  display:none;
  align-items:center;
  justify-content:center;
  z-index:1000;
  padding:20px;
}

.popup-content{
  background:linear-gradient(135deg,#0f0f0f,#1a1a1a);
  border:1px solid rgba(212,175,55,0.3);
  border-radius:8px;
  padding:50px;
  max-width:550px;
  text-align:center;
  box-shadow:0 0 80px rgba(212,175,55,0.2);
  animation:popupSlide 0.5s cubic-bezier(0.4,0,0.2,1);
  position:relative;
}

.popup-content::before{
  content:'';
  position:absolute;
  inset:0;
  border-radius:8px;
  padding:1px;
  background:linear-gradient(135deg,#d4af37,transparent,#c9a961);
  -webkit-mask:linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite:xor;
  mask-composite:exclude;
  opacity:0.3;
}

@keyframes popupSlide{
  from{transform:scale(0.9) translateY(-30px);opacity:0;}
  to{transform:scale(1) translateY(0);opacity:1;}
}

.popup-content h2{
  font-family:'Playfair Display',serif;
  font-size:38px;
  color:#d4af37;
  margin-bottom:25px;
  letter-spacing:3px;
  font-weight:700;
}

.popup-content p{
  color:#d0cfc5;
  font-size:17px;
  line-height:1.9;
  margin-bottom:40px;
  font-weight:300;
  letter-spacing:0.5px;
}

.popup-close{
  background:linear-gradient(135deg,#d4af37,#c9a961);
  color:#0a0a0a;
  border:none;
  padding:16px 45px;
  font-size:13px;
  font-weight:600;
  text-transform:uppercase;
  letter-spacing:3px;
  border-radius:4px;
  cursor:pointer;
  transition:all 0.4s cubic-bezier(0.4,0,0.2,1);
  font-family:'Montserrat',sans-serif;
}

.popup-close:hover{
  transform:translateY(-3px);
  box-shadow:0 10px 40px rgba(212,175,55,0.4);
  background:linear-gradient(135deg,#f4e4c1,#d4af37);
}

@media(max-width:768px){
  .cta-buttons{flex-direction:column;align-items:center;}
  .btn{width:100%;max-width:320px;}
  .brand-logo{letter-spacing:10px;}
  .popup-content{padding:40px 30px;}
}
</style>
</head>

<body>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1 class="brand-logo">SHOP HOLY</h1>
    <div class="cta-buttons">
      <a href="iniciar_sesion.php" class="btn btn-primary">Iniciar Sesión</a>
      <a href="registro.php" class="btn btn-secondary">Registrarse</a>
    </div>
  </div>
  <div class="scroll-indicator">
    <i class="fas fa-chevron-down"></i><br>
    DESCUBRE MÁS
  </div>
</section>

<section class="about">
  <h2>NUESTRA ESENCIA</h2>
  <p>Shop Holy redefine el concepto de streetwear de lujo. Somos una casa de moda que celebra la elegancia urbana, donde cada prenda es una obra de arte diseñada para quienes aprecian la exclusividad y el estilo atemporal.</p>
  
  <div class="features">
    <div class="feature-card" onclick="openPopup('streetwear')">
      <div class="feature-icon"><i class="fas fa-crown"></i></div>
      <h3>Diseño Exclusivo</h3>
      <p>Creaciones únicas que fusionan elegancia contemporánea con la audacia del streetwear</p>
    </div>
    
    <div class="feature-card" onclick="openPopup('quality')">
      <div class="feature-icon"><i class="fas fa-gem"></i></div>
      <h3>Calidad Superior</h3>
      <p>Materiales premium seleccionados meticulosamente para garantizar durabilidad y confort</p>
    </div>
    
    <div class="feature-card" onclick="openPopup('exclusive')">
      <div class="feature-icon"><i class="fas fa-certificate"></i></div>
      <h3>Ediciones Limitadas</h3>
      <p>Colecciones exclusivas en cantidades limitadas para los más exigentes</p>
    </div>
  </div>
</section>

<!-- COLLECTIONS -->
<section class="collections">
  <h2>COLECCIONES</h2>
  <div class="collection-grid">
    <div class="collection-item">
      <img src="https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=800" alt="Urban">
      <div class="collection-overlay">
        <h3>ELEGANCIA</h3>
      </div>
    </div>
    <div class="collection-item">
      <img src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800" alt="Minimal">
      <div class="collection-overlay">
        <h3>ESENCIA</h3>
      </div>
    </div>
    <div class="collection-item">
      <img src="https://images.unsplash.com/photo-1529374255404-311a2a4f1fd9?w=800" alt="Bold">
      <div class="collection-overlay">
        <h3>PRESTIGIO</h3>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-content">
    <h3>SHOP HOLY</h3>
    <p>Síguenos en nuestras redes sociales</p>
    <div class="social-links">
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-tiktok"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-facebook"></i></a>
    </div>
    <p>© 2025 Shop Holy. Todos los derechos reservados.</p>
  </div>
</footer>

<!-- POPUP -->
<div class="popup" id="popup">
  <div class="popup-content">
    <h2 id="popup-title"></h2>
    <p id="popup-text"></p>
    <button class="popup-close" onclick="closePopup()">Cerrar</button>
  </div>
</div>

<script>
const popupData = {
  streetwear: {
    title: '👑 DISEÑO EXCLUSIVO',
    text: 'Cada pieza de Shop Holy es concebida por diseñadores visionarios que entienden el equilibrio perfecto entre la estética urbana y la sofisticación. Nuestras creaciones son el resultado de meses de investigación en tendencias globales y artesanía impecable.'
  },
  quality: {
    title: '💎 CALIDAD SUPERIOR',
    text: 'Seleccionamos solo los tejidos más finos del mundo. Desde algodones egipcios hasta mezclas técnicas de última generación, cada material pasa por rigurosos controles de calidad. La excelencia no es negociable en Shop Holy.'
  },
  exclusive: {
    title: '🏆 EDICIONES LIMITADAS',
    text: 'Nuestras colecciones exclusivas se producen en series numeradas y limitadas. Cuando adquieres una pieza de Shop Holy, no solo compras ropa, inviertes en una obra de arte portable que muy pocos en el mundo poseerán.'
  }
};

function openPopup(type){
  const data = popupData[type];
  document.getElementById('popup-title').textContent = data.title;
  document.getElementById('popup-text').textContent = data.text;
  document.getElementById('popup').style.display = 'flex';
}

function closePopup(){
  document.getElementById('popup').style.display = 'none';
}

// Cerrar popup con ESC
document.addEventListener('keydown', function(e){
  if(e.key === 'Escape') closePopup();
});

// Cerrar popup al hacer click fuera
document.getElementById('popup').addEventListener('click', function(e){
  if(e.target === this) closePopup();
});
</script>

</body>
</html>