import './styles.css';

const app = document.querySelector('#app');

app.innerHTML = `
  <div class="dn-shell">
    <aside class="dn-sidebar">
      <div class="dn-brand">
        <div class="dn-brand-mark">D</div>
        <div>
          <p class="dn-brand-title">Trix UI</p>
          <p class="dn-brand-subtitle">Design system</p>
        </div>
      </div>

      <nav class="dn-nav" aria-label="Main navigation">
        <a href="#" class="dn-nav-item active">Overview</a>
        <a href="#" class="dn-nav-item">Components</a>
        <a href="#" class="dn-nav-item">Layouts</a>
        <a href="#" class="dn-nav-item">Themes</a>
        <a href="#" class="dn-nav-item">Reports</a>
      </nav>

      <div class="dn-theme-panel">
        <span>Theme</span>
        <button class="dn-btn dn-btn-ghost dn-btn-sm" data-theme-switcher="default" type="button">Default</button>
        <button class="dn-btn dn-btn-soft dn-btn-sm" data-theme-switcher="ocean" type="button">Ocean</button>
        <button class="dn-btn dn-btn-soft dn-btn-sm" data-theme-switcher="forest" type="button">Forest</button>
      </div>
    </aside>

    <main class="dn-main">
      <header class="dn-topbar">
        <div>
          <p class="dn-eyebrow">UI kit</p>
          <h1>Component foundation</h1>
        </div>
        <div class="dn-actions">
          <button class="dn-btn dn-btn-soft" type="button">Preview</button>
          <button class="dn-btn dn-btn-primary" type="button">Ship release</button>
        </div>
      </header>

      <section class="dn-grid dn-grid-4">
        <article class="dn-card dn-card-metric">
          <p class="dn-metric-label">Components</p>
          <p class="dn-metric-value">58</p>
          <span class="dn-metric-trend positive">+12% this month</span>
        </article>
        <article class="dn-card dn-card-metric">
          <p class="dn-metric-label">Layouts</p>
          <p class="dn-metric-value">21</p>
          <span class="dn-metric-trend positive">+3 launches</span>
        </article>
        <article class="dn-card dn-card-metric">
          <p class="dn-metric-label">Accessibility</p>
          <p class="dn-metric-value">AA+</p>
          <span class="dn-metric-trend neutral">Contrast pass</span>
        </article>
        <article class="dn-card dn-card-metric">
          <p class="dn-metric-label">Theme modes</p>
          <p class="dn-metric-value">14</p>
          <span class="dn-metric-trend neutral">Light + dark</span>
        </article>
      </section>

      <section class="dn-panels">
        <article class="dn-card dn-card-feature">
          <div class="dn-card-head">
            <div>
              <p class="dn-kicker">Launch checklist</p>
              <h2>Product rollout summary</h2>
            </div>
            <button class="dn-btn dn-btn-ghost dn-btn-sm" type="button">Review all</button>
          </div>

          <div class="dn-alert dn-alert-info">
            <strong>Heads up:</strong> Theme tokens and semantic styles are now synced across the default and ocean presets.
          </div>

          <div class="dn-list-stack">
            <div class="dn-list-row">
              <div>
                <span class="dn-list-title">Button primitives</span>
                <small>Primary, soft, ghost, danger</small>
              </div>
              <span class="dn-tag dn-tag-success">Ready</span>
            </div>
            <div class="dn-list-row">
              <div>
                <span class="dn-list-title">Form controls</span>
                <small>Input, select, switch, radio</small>
              </div>
              <span class="dn-tag dn-tag-info">In review</span>
            </div>
            <div class="dn-list-row">
              <div>
                <span class="dn-list-title">Sidebar navigation</span>
                <small>Dashboard shell, mobile stack</small>
              </div>
              <span class="dn-tag dn-tag-warning">Testing</span>
            </div>
          </div>
        </article>

        <article class="dn-card">
          <div class="dn-card-head">
            <div>
              <p class="dn-kicker">Quick capture</p>
              <h2>New record</h2>
            </div>
            <span class="dn-badge">Draft</span>
          </div>
          <div class="dn-form-grid">
            <label class="dn-field">
              <span>Project name</span>
              <input class="dn-input" type="text" value="Trix UI kit" />
            </label>
            <label class="dn-field">
              <span>Owner</span>
              <select class="dn-input">
                <option>Design pod</option>
                <option>Engineering</option>
                <option>Marketing</option>
              </select>
            </label>
            <label class="dn-field dn-field-full">
              <span>Notes</span>
              <textarea class="dn-input dn-textarea" rows="4">Build reusable dashboard variants and revise card density.</textarea>
            </label>
          </div>
          <div class="dn-actions dn-actions-tight">
            <button class="dn-btn dn-btn-soft" type="button">Save draft</button>
            <button class="dn-btn dn-btn-primary" type="button">Publish</button>
          </div>
        </article>
      </section>

      <section class="dn-panels">
        <article class="dn-card dn-card-wide">
          <div class="dn-card-head">
            <div>
              <p class="dn-kicker">Operations</p>
              <h2>Recent orders</h2>
            </div>
            <span class="dn-badge dn-badge-success">Live</span>
          </div>

          <table class="dn-table">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Product</th>
                <th>Status</th>
                <th>Value</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Nova Labs</td>
                <td>Enterprise plan</td>
                <td><span class="dn-tag dn-tag-success">Paid</span></td>
                <td>$3,600</td>
              </tr>
              <tr>
                <td>Bluebird Studio</td>
                <td>Starter bundle</td>
                <td><span class="dn-tag dn-tag-warning">Pending</span></td>
                <td>$760</td>
              </tr>
              <tr>
                <td>Northwind</td>
                <td>Platform access</td>
                <td><span class="dn-tag dn-tag-info">Processing</span></td>
                <td>$24,000</td>
              </tr>
              <tr>
                <td>Harbor Labs</td>
                <td>Design retainer</td>
                <td><span class="dn-tag dn-tag-success">Paid</span></td>
                <td>$5,200</td>
              </tr>
            </tbody>
          </table>
        </article>

        <article class="dn-card">
          <div class="dn-card-head">
            <div>
              <p class="dn-kicker">Activity</p>
              <h2>Recent updates</h2>
            </div>
            <span class="dn-badge">Today</span>
          </div>

          <div class="dn-feed">
            <div class="dn-feed-item">
              <span class="dn-feed-dot"></span>
              <div>
                <strong>Theme tokens synced</strong>
                <small>New default + ocean preset</small>
              </div>
            </div>
            <div class="dn-feed-item">
              <span class="dn-feed-dot"></span>
              <div>
                <strong>Button states reviewed</strong>
                <small>Hover, focus, disabled, danger</small>
              </div>
            </div>
            <div class="dn-feed-item">
              <span class="dn-feed-dot"></span>
              <div>
                <strong>Mock dashboard shipped</strong>
                <small>Layouts and cards ready for QA</small>
              </div>
            </div>
          </div>
        </article>
      </section>

      <section class="dn-panels">
        <article class="dn-card">
          <div class="dn-navbar">
            <div class="dn-navbar-brand">
              <div class="dn-navbar-mark">D</div>
              <span>Trix</span>
            </div>
            <nav class="dn-navbar-nav" aria-label="Navbar">
              <a href="#">Overview</a>
              <a href="#">Workflows</a>
              <a href="#">Billing</a>
              <a href="#">Settings</a>
            </nav>
            <div class="dn-actions">
              <button class="dn-btn dn-btn-soft dn-btn-sm" type="button">Share</button>
              <button class="dn-btn dn-btn-primary dn-btn-sm" type="button">New project</button>
            </div>
          </div>

          <div class="dn-check-list">
            <label class="dn-toggle">
              <input type="checkbox" checked />
              <span>Auto sync design tokens</span>
            </label>
            <label class="dn-toggle">
              <input type="checkbox" />
              <span>Require approval before publish</span>
            </label>
            <label class="dn-toggle">
              <input type="checkbox" checked />
              <span>Enable dark mode preview</span>
            </label>
          </div>
        </article>

        <article class="dn-card dn-modal-surface">
          <div class="dn-modal-card" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <p class="dn-kicker">Confirm action</p>
            <h3 id="modal-title">Publish this release?</h3>
            <p>This will share the new component set with all active workspaces and apply the current theme preset.</p>
            <div class="dn-actions dn-actions-tight">
              <button class="dn-btn dn-btn-ghost" type="button">Cancel</button>
              <button class="dn-btn dn-btn-primary" type="button">Confirm publish</button>
            </div>
          </div>
        </article>
      </section>
    </main>
  </div>
`;

const themeButtons = document.querySelectorAll('[data-theme-switcher]');

themeButtons.forEach((button) => {
  button.addEventListener('click', () => {
    const next = button.dataset.themeSwitcher;
    const root = document.documentElement;
    root.dataset.theme = next;
    localStorage.setItem('trix-ui-theme', next);
  });
});
