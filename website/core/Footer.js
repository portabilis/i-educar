/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

const React = require('react');

class Footer extends React.Component {
  docUrl(doc, language) {
    const baseUrl = this.props.config.baseUrl;
    return baseUrl + 'docs/' + (language ? language + '/' : '') + doc;
  }

  pageUrl(doc, language) {
    const baseUrl = this.props.config.baseUrl;
    return baseUrl + (language ? language + '/' : '') + doc;
  }

  render() {
    const currentYear = new Date().getFullYear();
    return (
      <footer id="footer" className="nav-footer" >
        <section className="sitemap">
          <a href={this.props.config.baseUrl} className="nav-home">
            {this.props.config.footerIcon && (
              <img
                className="footerLogo"
                src={this.props.config.baseUrl + this.props.config.footerIcon}
                alt={this.props.config.title}
                width="108"
              />
            )}
          </a>
          <div className="footerNav">
            <nav>
              <ul>
                <li>
                  <a href="index">Home</a>
                </li>
                <li>
                  <a href="index.html#quemusa" >Quem Usa?</a>
                </li>
                <li>
                  <a href = "">Blog</a>
                </li>
                <li>
                  <a href="https://forum.ieducar.org">Fórum</a>
                </li>
                <li>
                  <a href="" >Documentação</a>
                </li>
              </ul>
            </nav>
          </div>
        </section>
        <section className='subfooter'>
          <div className='subfooteritens'>
              <div className = "footerDescription">
                <p>Mantido pela <img src={this.props.config.baseUrl+'img/logo-portabilis.svg'} /> e Comunidade i-Educar para educação do Brasil <img src={this.props.config.baseUrl +'img/coracao.svg'} /> </p>
              </div>
              <div className ="footerVersion">
                <p>v. i-Educar</p>
              </div>
           </div>
        </section>
      </footer>
    );
  }
}

module.exports = Footer;
