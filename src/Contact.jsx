import React from "react";
import "./App.css";
import contact from "./assets/contact.jpg";

function Contact() {
  return (
    <div className="container-fluid">
      <div className="row">
        <div
          className="footer footer-heading footer-text col-lg-4 col-sm-12"
          id="contact"
        >
          <div className="contact-text">
            <h3>Contact Me</h3>
            <p>
              I’d love to hear from you! Whether you have a project in mind or
              just want to chat about your web needs, feel free to reach out.
            </p>
            <p>
              Phone: <a href="tel:03234735833">03234735833</a>
            </p>
            <p>
              Email:{" "}
              <a href="mailto:asn.tanoli@gmail.com">asn.tanoli@gmail.com</a>
            </p>
            <a
              href="mailto:asn.tanoli@gmail.com"
              className="cta-button-contact"
            >
              Send a Message
            </a>
          </div>
        </div>

        <div className="footer footer-heading footer-text col-lg-4 col-sm-12 text-lg-center">
          <div className="contact-text">
            <h3>My Projects</h3>
            <a href="https://dev-ahsandigital.pantheonsite.io/" target="_blank">
              Small Spark
            </a>
            <br />
            <a href="https://dev-ahsanwp.pantheonsite.io/" target="_blank">
              Sphere
            </a>
            <br />
            <a
              href="https://dev-ahsantanoli.pantheonsite.io/coming-soon/"
              target="_blank"
            >
              Coming-Soon Landing Page
            </a>
            <br />
            <a href="https://dev-ahsantanoli.pantheonsite.io/" target="_blank">
              Marvel Landing Page
            </a>
            <br />
            <a
              href="https://dev-ahsantanoli.pantheonsite.io/vr-gaming/"
              target="_blank"
            >
              VR-Gaming Landing Page
            </a>
            <br />
            <a
              href="https://dev-ahsantanoli.pantheonsite.io/waisee-design/"
              target="_blank"
            >
              Waisee-Design
            </a>
            <br />
            <a href="https://ahsanwp.subkoch.com/portfolio2/" target="_blank">
              DeMastry
            </a>
            <br />
            <a href="https://dev-ahsanwebs.pantheonsite.io/" target="_blank">
              Vertical Header
            </a>
            <br />
          </div>
        </div>

        <div className="footer footer-heading footer-text col-lg-4 col-sm-12 text-lg-center">
          <div className="contact-text">
            <h3>Social Media Links</h3>
            <a href="https://www.facebook.com/Asn376" target="_blank">
              Facebook
            </a>
            <br />
            <a href="https://www.instagram.com/ahsanmtanoli/" target="_blank">
              Instagram
            </a>
            <br />
            <a href="https://github.com/Ahsanmtanoli" target="_blank">
              GitHub
            </a>
            <br />
            <a
              href="https://www.linkedin.com/in/hafiz-ahsan-mahmood-8b5b84105/"
              target="_blank"
            >
              LinkedIn
            </a>
            <br />
            <a href="https://twitter.com/ahsanmtanoli" target="_blank">
              Twitter
            </a>
            <br />
          </div>
        </div>
      </div>
      <div class className="container-fluid">
        <div className="row">
          <div className="col-sm-12">Made By Ahsan Mahmood with Love</div>
        </div>
      </div>
    </div>
  );
}

export default Contact;
