/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

const React = require('react');

const CompLibrary = require('../../core/CompLibrary.js');
const MarkdownBlock = CompLibrary.MarkdownBlock; /* Used to read markdown */
const Container = CompLibrary.Container;
const GridBlock = CompLibrary.GridBlock;
const BlogGridBlock = require(process.cwd() + '/core/BlogGridBlock.js');
const MapBlock = require(process.cwd() + '/core/MapBlock.js');

//const MetadataBlog = require('./MetadataBlog.js');
//var threeLastPost = MetadataBlog.slice(0,3);


const siteConfig = require(process.cwd() + '/siteConfig.js');

function imgUrl(img) {
  return siteConfig.baseUrl + 'img/' + img;
}

function docUrl(doc, language) {
  return siteConfig.baseUrl + 'docs/' + (language ? language + '/' : '') + doc;
}

function pageUrl(page, language) {
  return siteConfig.baseUrl + (language ? language + '/' : '') + page;
}

class Button extends React.Component {
  render() {
    return (
      <div className="pluginWrapper buttonWrapper">
        <a className={this.props.className} href={this.props.href} target={this.props.target}>
          {this.props.children}
        </a>
      </div>
    );
  }
}

class InputText extends React.Component {
  render() {
    return (
      <div className="pluginWrapper buttonWrapper">
        <label className={this.props.classNameLabel}>{this.props.labelName}</label>
        <input type="text" id={this.props.id} className={this.props.className}/>
      </div>
    );
  }
}


Button.defaultProps = {
  target: '_self',
};

const SplashContainer = props => (
  <div className="homeContainer">
    <div className="homeSplashFade">
      <div className="wrapper homeWrapper">{props.children}</div>
    </div>
  </div>
);

const Logo = props => (
  <div className="projectLogo">
    <img src={props.img_src} />
  </div>
);

const ProjectTitle = props => (
  <h2 className="projectTitle">
    {siteConfig.title}
    <small>{siteConfig.tagline}</small>
  </h2>
);

const PromoSection = props => (
  <div className="section promoSection">
    <div className="promoRow">
      <div className="pluginRowBlock">{props.children}</div>
    </div>
  </div>
);

class HomeSplash extends React.Component {
  render() {
    let language = this.props.language || '';
    return (
      <SplashContainer>
        {/* <Logo img_src={imgUrl('i-educar-logo.png')} /> */}
        <div className="inner">
          
        </div>
      </SplashContainer>
    );
  }
}

const Block = props => (
  <Container
    padding={['bottom', 'top']}
    id={props.id}
    background={props.background}
    className={props.className}>
    <GridBlock className={props.classNameGrid} align="left" contents={props.children} layout={props.layout} />
  </Container>
);

const BlogBlock = props => (
  <Container
    padding={['bottom', 'top']}
    id={props.id}
    background={props.background}
    className={props.className}>
    <BlogGridBlock className={props.classNameGrid} align="left" layout={props.layout} />
  </Container>
)

const BlockHeader = props => (
  <Container
    padding={['bottom', 'top']}
    id={props.id}
    background={props.background}
    className={props.class}
    >
    <GridBlock className={props.classNameGrid} align="left" contents={props.children} layout={props.layout} />
  </Container>
);

const Features = props => (
  <div>
  <BlockHeader layout="OneColumn" class="featureContainerHeader" classNameGrid="blockHeader">
    {[
      {
        title: 'Principais Funcionalidades',
        textAlign: 'left',
      }
    ]}
  </BlockHeader>
  <Block layout="threeColumn" className="featureContainer" classNameGrid="featureBlock">
    {[
      {
        content: 'Acessivel de qualquer lugar e com cadastro único do aluno disponível para toda a rede de ensino. Economia de tempo para todos!',
        image: imgUrl('funcionalidades/wifi.png'),
        imageAlign: 'top',
        title: 'Totalmente Online',
      },
      {
        content: 'Obtenha dados quantitativos, financeiros e estatisticos sempre atuais de todos os processos, na hora e lugar que você deseja!',
        image: imgUrl('funcionalidades/page-clock.png'),
        imageAlign: 'top',
        title: 'Informações em tempo real',
      },
      {
        content: 'Sistema de avaliação e relatórios adaptados para as dferentes realidades do pais, com notas numéricas, conceituais ou avaliação descritiva.',
        image: imgUrl('funcionalidades/page.png'),
        imageAlign: 'top',
        title: 'Sistema de avaliação flexivel',
      }
    ]}
  </Block>
  <Block layout="threeColumn" className="featureContainer" classNameGrid="featureBlock">
    {[
      {
        content: 'Gerenciamento de alocações, afastamentos, substituições, faltas e atrasos, oferecendo uma visão integrada de todos os profissionais.',
        image: imgUrl('funcionalidades/user.png'),
        imageAlign: 'top',
        title: 'Gestão de servidores',
      },
      {
        content: 'Gereção de quadro de horário para análise das demandas e disponibilidades de profissionais na rede de ensino em cada periodo letivo.',
        image: imgUrl('funcionalidades/calendar-clock.png'),
        imageAlign: 'top',
        title: 'Quadro de horário',
      },
      {
        content: 'Controle de veiculos, motoristas, rotas, itinerários, emissão de carteirinha e controle de usuários do tranporte.',
        image: imgUrl('funcionalidades/bus.png'),
        imageAlign: 'top',
        title: 'Transporte escolar',
      }
    ]}
  </Block>
  <Block layout="threeColumn" className="featureContainerLast" classNameGrid="featureBlock">
    {[
      {
        content: 'Gestão completa de acervo com controle de reservas, emprestimos e devolução .Recibos de controle disponiveis para cada fase do processo.',
        image: imgUrl('funcionalidades/book.png'),
        imageAlign: 'top',
        title: 'Biblioteca',
      },
      {
        content: 'Usufrua de uma  coleção  de mais 150 modelos de relatórios e documentos Boletins, historia, ficha, atestados e gráficos.',
        image: imgUrl('funcionalidades/graph.png'),
        imageAlign: 'top',
        title: 'Relatórios e indicadores ',
      },
      {
        content: 'Controle de todas as informaçoes para o Censo Escolar com recursos para importação e exportação automatizada dos dados.',
        image: imgUrl('funcionalidades/e-clip.png'),
        imageAlign: 'top',
        title: 'Educacenso/INEP',
      }
    ]}
  </Block>
  </div>
);
const Blog = props => (
  <div>
  <BlockHeader layout="OneColumn"  class="blogContainerHeader" classNameGrid="blogBlockHeader">
    {[
      {
        title: 'Blog',
        content: 'Fique por dentro das novidades do i-Educar!',
        textAlign: 'left',
      }
    ]}
  </BlockHeader>
  <BlogBlock layout="threeColumn" className="blogContainerLast" classNameGrid="blogBlock" />
  </div>
);

const Showcase = props => {
  if ((siteConfig.users || []).length === 0) {
    return null;
  }
  const showcase = siteConfig.users
    .filter(user => {
      return user.pinned;
    })
    .map((user, i) => {
      return (
        <a href={user.infoLink} key={i}>
          <img src={user.image} alt={user.caption} title={user.caption} />
        </a>
      );
    });

  return (
    <div className="productShowcaseSection paddingBottom" id="quemusa">
      <h2>{"Redes de ensino que usam o i-Educar"}</h2>
      <div className="logos">{showcase}</div>
    </div>
  );
};

class Index extends React.Component {
  render() {
    let language = this.props.language || '';

    return (
      <div>
        <HomeSplash language={language} />
        <div className="mainContainer">
          <Features />
          <Blog />
          <Showcase language={language} />
          <MapBlock id="mapBlock" />
        </div>
      </div>
    );
  }
}

module.exports = Index;
