import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import App from "./Header.jsx";
import Home from "./Home.jsx";
import About from "./About.jsx";
import Services from "./services.jsx";
import Contact from "./Contact.jsx";
import Header from "./Header.jsx";
import "bootstrap/dist/css/bootstrap.min.css";
import "./index.css";
import "./Contact.css";

createRoot(document.getElementById("root")).render(
  <StrictMode>
    <Header />
    <Home />
    <About />
    <Services />
    <Contact />
  </StrictMode>
);
