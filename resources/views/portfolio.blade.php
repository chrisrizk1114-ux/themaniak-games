<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Chris Rizk — Full-Stack Developer Portfolio. Laravel, PHP, JavaScript. Builder of themaniak.online." />
  <title>Chris Rizk — Portfolio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg: #ffffff;
      --text: #111111;
      --muted: #555555;
      --border: #e5e5e5;
      --soft: #f7f7f7;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
      font-family: Inter, Calibri, Arial, sans-serif;
      font-size: 16px;
      line-height: 1.6;
      color: var(--text);
      background: var(--bg);
    }

    a { color: var(--text); text-decoration: underline; text-underline-offset: 3px; }
    a:hover { opacity: 0.7; }

    .wrap {
      max-width: 900px;
      margin: 0 auto;
      padding: 0 24px;
    }

    nav {
      position: sticky;
      top: 0;
      z-index: 10;
      background: rgba(255,255,255,0.95);
      border-bottom: 1px solid var(--border);
      backdrop-filter: blur(8px);
    }

    nav .wrap {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 14px;
      padding-bottom: 14px;
    }

    nav .logo {
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      color: var(--text);
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    nav a {
      font-size: 14px;
      text-decoration: none;
      color: var(--muted);
      font-weight: 500;
    }

    nav a:hover { color: var(--text); opacity: 1; }

    .hero {
      padding: 72px 0 56px;
      border-bottom: 1px solid var(--border);
    }

    .hero .label {
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 12px;
    }

    .hero h1 {
      font-size: clamp(2.2rem, 6vw, 3.2rem);
      font-weight: 700;
      line-height: 1.1;
      letter-spacing: -0.02em;
      margin-bottom: 16px;
    }

    .hero .tagline {
      font-size: 1.15rem;
      color: var(--muted);
      max-width: 600px;
      margin-bottom: 28px;
    }

    .hero-links {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .btn {
      display: inline-block;
      padding: 10px 18px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      border: 1.5px solid var(--text);
      color: var(--text);
      border-radius: 6px;
      transition: background 0.15s, color 0.15s;
    }

    .btn:hover { background: var(--text); color: #fff; opacity: 1; }

    .btn-primary {
      background: var(--text);
      color: #fff;
    }

    .btn-primary:hover { background: #333; border-color: #333; }

    section {
      padding: 56px 0;
      border-bottom: 1px solid var(--border);
    }

    section:last-of-type { border-bottom: none; }

    h2 {
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 24px;
    }

    h3 {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .meta {
      font-size: 14px;
      color: var(--muted);
      margin-bottom: 8px;
    }

    .about p {
      color: var(--muted);
      max-width: 680px;
    }

    .skill-groups {
      display: grid;
      gap: 20px;
    }

    .skill-group strong {
      display: block;
      font-size: 14px;
      margin-bottom: 8px;
    }

    .tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .tag {
      font-size: 13px;
      font-weight: 500;
      padding: 5px 12px;
      border: 1px solid var(--border);
      border-radius: 999px;
      background: var(--soft);
      color: var(--text);
    }

    .project {
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 24px;
      background: var(--soft);
    }

    .project h3 { font-size: 1.2rem; margin-bottom: 6px; }

    .project .stack {
      font-size: 13px;
      color: var(--muted);
      margin-bottom: 12px;
    }

    .project p {
      font-size: 15px;
      color: var(--muted);
      margin-bottom: 14px;
    }

    .project ul {
      margin: 0 0 16px 18px;
      color: var(--muted);
      font-size: 15px;
    }

    .project li { margin-bottom: 4px; }

    .project-links {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .game-links {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 12px;
    }

    .game-links a {
      font-size: 12px;
      font-weight: 500;
      text-decoration: none;
      padding: 4px 10px;
      border: 1px solid var(--border);
      border-radius: 5px;
      background: #fff;
      color: var(--text);
    }

    .game-links a:hover { background: var(--text); color: #fff; opacity: 1; }

    .entry {
      margin-bottom: 24px;
      padding-bottom: 24px;
      border-bottom: 1px solid var(--border);
    }

    .entry:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .entry-row {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      gap: 12px;
      flex-wrap: wrap;
    }

    .entry-date {
      font-size: 14px;
      color: var(--muted);
      white-space: nowrap;
    }

    .entry p { font-size: 15px; color: var(--muted); margin-top: 6px; }

    .contact-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
    }

    .contact-item strong {
      display: block;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      margin-bottom: 4px;
    }

    .contact-item span, .contact-item a {
      font-size: 15px;
      text-decoration: none;
      color: var(--text);
    }

    footer {
      padding: 32px 0;
      text-align: center;
      font-size: 13px;
      color: var(--muted);
      border-top: 1px solid var(--border);
    }

    @media (max-width: 600px) {
      nav ul { gap: 14px; }
      .hero { padding: 48px 0 40px; }
      section { padding: 40px 0; }
    }
  </style>
</head>
<body>

  <nav>
    <div class="wrap">
      <a class="logo" href="#">Chris Rizk</a>
      <ul>
        <li><a href="#about">About</a></li>
        <li><a href="#projects">Projects</a></li>
        <li><a href="#skills">Skills</a></li>
        <li><a href="#experience">Experience</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </div>
  </nav>

  <header class="hero">
    <div class="wrap">
      <p class="label">Portfolio</p>
      <h1>Chris Rizk</h1>
      <p class="tagline">
        Third-year Computer Science student &amp; aspiring full-stack developer.
        I build and ship web applications with PHP, Laravel, and JavaScript.
      </p>
      <div class="hero-links">
        <a class="btn btn-primary" href="https://themaniak.online" target="_blank" rel="noopener">View Live Project</a>
        <a class="btn" href="#contact">Get in Touch</a>
      </div>
    </div>
  </header>

  <section id="about" class="about">
    <div class="wrap">
      <h2>About</h2>
      <p>
        I'm a Computer Science student at Antonine University with hands-on experience
        building real web applications from scratch. I designed, developed, and deployed
        a full-stack mini games platform used by real users — handling everything from
        backend architecture and database design to responsive front-end interfaces and
        production deployment with Docker.
      </p>
    </div>
  </section>

  <section id="projects">
    <div class="wrap">
      <h2>Featured Project</h2>

      <article class="project">
        <h3>Mini Games Platform</h3>
        <p class="stack">PHP · Laravel · JavaScript · HTML/CSS · MySQL · Docker · MVC</p>
        <p>
          A full-stack web platform with user authentication, leaderboards, friend system,
          real-time chat, online multiplayer chess, and multiple browser-based games —
          built and deployed to production.
        </p>
        <ul>
          <li>Backend: Laravel MVC, auth, REST-style routes, Eloquent models, MySQL database</li>
          <li>Frontend: responsive game UIs in JavaScript with canvas/DOM-based gameplay</li>
          <li>Features: leaderboards, user profiles, friend invites, chess multiplayer, admin panel</li>
          <li>DevOps: Dockerized deployment on Render with PHP 8.2 and cloud MySQL</li>
        </ul>
        <div class="project-links">
          <a class="btn btn-primary" href="https://themaniak.online" target="_blank" rel="noopener">themaniak.online</a>
        </div>
        <div class="game-links">
          <a href="https://themaniak.online/chess" target="_blank" rel="noopener">Chess</a>
          <a href="https://themaniak.online/platformer" target="_blank" rel="noopener">Platformer</a>
          <a href="https://themaniak.online/whack-a-mole" target="_blank" rel="noopener">Whack-a-Mole</a>
          <a href="https://themaniak.online/galaxy-bowling" target="_blank" rel="noopener">Bowling</a>
          <a href="https://themaniak.online/tic-tac-toe" target="_blank" rel="noopener">Tic-Tac-Toe</a>
        </div>
      </article>
    </div>
  </section>

  <section id="skills">
    <div class="wrap">
      <h2>Skills</h2>
      <div class="skill-groups">
        <div class="skill-group">
          <strong>Backend</strong>
          <div class="tags">
            <span class="tag">PHP 8.2</span>
            <span class="tag">Laravel</span>
            <span class="tag">MVC</span>
            <span class="tag">REST APIs</span>
            <span class="tag">Eloquent ORM</span>
            <span class="tag">Authentication</span>
          </div>
        </div>
        <div class="skill-group">
          <strong>Frontend</strong>
          <div class="tags">
            <span class="tag">JavaScript</span>
            <span class="tag">HTML5</span>
            <span class="tag">CSS3</span>
            <span class="tag">Responsive Design</span>
          </div>
        </div>
        <div class="skill-group">
          <strong>Database &amp; Tools</strong>
          <div class="tags">
            <span class="tag">MySQL</span>
            <span class="tag">SQL</span>
            <span class="tag">Git</span>
            <span class="tag">Docker</span>
            <span class="tag">Linux</span>
            <span class="tag">SAP</span>
          </div>
        </div>
        <div class="skill-group">
          <strong>Languages</strong>
          <div class="tags">
            <span class="tag">English</span>
            <span class="tag">French</span>
            <span class="tag">Arabic</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="experience">
    <div class="wrap">
      <h2>Experience</h2>

      <article class="entry">
        <div class="entry-row">
          <h3>Database Intern</h3>
          <span class="entry-date">Sep 2024 – Nov 2024</span>
        </div>
        <p class="meta">Hotel-Dieu de France Hospital · Achrafieh, Beirut</p>
        <p>SQL database querying and SAP-based data management in a hospital IT environment.</p>
      </article>

      <article class="entry">
        <div class="entry-row">
          <h3>Picker</h3>
          <span class="entry-date">Jul 2025 – Sep 2025</span>
        </div>
        <p class="meta">Toters · Achrafieh, Beirut</p>
      </article>

      <article class="entry">
        <div class="entry-row">
          <h3>Waiter</h3>
          <span class="entry-date">Jun 2024 – Aug 2024</span>
        </div>
        <p class="meta">Pro's Cafe · Dekwaneh, Beirut</p>
      </article>

      <h2 style="margin-top: 40px;">Education</h2>

      <article class="entry">
        <div class="entry-row">
          <h3>Bachelor of Computer Science</h3>
        </div>
        <p class="meta">Antonine University · Third Year, In Progress</p>
      </article>

      <article class="entry">
        <div class="entry-row">
          <h3>Lebanese Baccalaureate, Terminal SG</h3>
        </div>
        <p class="meta">Al Akhtal Al Saghir School</p>
      </article>
    </div>
  </section>

  <section id="contact">
    <div class="wrap">
      <h2>Contact</h2>
      <div class="contact-grid">
        <div class="contact-item">
          <strong>Email</strong>
          <a href="mailto:chrisrizk47@gmail.com">chrisrizk47@gmail.com</a>
        </div>
        <div class="contact-item">
          <strong>Phone</strong>
          <span>+961 79 183 740</span>
        </div>
        <div class="contact-item">
          <strong>Location</strong>
          <span>Beirut, Lebanon</span>
        </div>
        <div class="contact-item">
          <strong>Website</strong>
          <a href="https://themaniak.online" target="_blank" rel="noopener">themaniak.online</a>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <div class="wrap">&copy; {{ date('Y') }} Chris Rizk. Built with HTML &amp; CSS.</div>
  </footer>

</body>
</html>
