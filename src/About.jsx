import React from 'react'; 
import './App.css';
import profileimage from './assets/Ahsan.jpeg'
 

function About() {
  return (
<div className='about' id="about">
    <div className='about-para'>

    <h1 className="about-header">About Me</h1>
    <p className="about-text">With extensive experience in WordPress, web design, and SEO, I am dedicated to delivering high-quality digital solutions that drive results. My background in HTML, CSS, and JavaScript enables me to build custom websites tailored to your needs. I pride myself on my attention to detail and commitment to client satisfaction.</p>
    </div>
    <div className='profile-image'>
    <img src={profileimage} id="image" />
    </div>
</div>

  )

}
export default About
