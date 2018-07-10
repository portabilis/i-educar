/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

// See https://docusaurus.io/docs/site-config.html for all the possible
// site configuration options.
/* List of projects/orgs using your project for the users page */
const users = [
  {
    caption: 'Prefeitura de Duque de Caxias',
    // You will need to prepend the image path with your baseUrl
    // if it is not '/', like: '/test-site/img/docusaurus.svg'.
    image: '/img/parceiros/duquecaxias@3x.png',
    pinned: true,
  },
  {
    caption: 'Prefeitura de Botucatu',
    // You will need to prepend the image path with your baseUrl
    // if it is not '/', like: '/test-site/img/docusaurus.svg'.
    image: '/img/parceiros/botucatu@3x.png',
    pinned: true,
  },
  {
    caption: 'Prefeitura de Criciúma',
    // You will need to prepend the image path with your baseUrl
    // if it is not '/', like: '/test-site/img/docusaurus.svg'.
    image: '/img/parceiros/criciuma@3x.png',
    pinned: true,
  },
  {
    caption: 'Prefeitura de Balneário de Camburiú',
    // You will need to prepend the image path with your baseUrl
    // if it is not '/', like: '/test-site/img/docusaurus.svg'.
    image: '/img/parceiros/balneario-camboriu@3x.png',
    pinned: true,
  },
  {
    caption: 'Prefeitura de Monte Alegre',
    // You will need to prepend the image path with your baseUrl
    // if it is not '/', like: '/test-site/img/docusaurus.svg'.
    image: '/img/parceiros/montealegre@3x.png',
    pinned: true,
  },
  {
    caption: 'Prefeitura de Paragominas',
    // You will need to prepend the image path with your baseUrl
    // if it is not '/', like: '/test-site/img/docusaurus.svg'.
    image: '/img/parceiros/paragominas@3x.png',
    pinned: true,
  },
  {
    caption: 'Prefeitura de São Miguel dos Campos',
    // You will need to prepend the image path with your baseUrl
    // if it is not '/', like: '/test-site/img/docusaurus.svg'.
    image: '/img/parceiros/sa-omigueldoscampos@3x.png',
    pinned: true,
  },
];

const statistics = {
  numberOfCities: '+80',
  numberOfSchools: '+2050',
  numberOfStudents: '+500.000'
}

const siteConfig = {
  title: 'i-Educar' /* title for your website */,
  tagline: 'O i-Educar é um software livre e público totalmente on-line que torna mais fácil e prática a gestão dos processos das escolas, matrículas e dados de alunos, apoiando os profissionais da rede de ensino e gestores.',
  disableHeaderTitle: true,
  url: 'http://ieducar.org' /* your website url */,
  baseUrl: '/' /* base url for your project */,
  // For github.io type URLs, you would set the url and baseUrl like:
  //   url: 'https://facebook.github.io',
  //   baseUrl: '/test-site/',
  cname: 'ieducar.org',

  // Used for publishing and more
  projectName: 'i-educar',
  organizationName: 'portabilis',
  // For top-level user or org sites, the organization is still the same.
  // e.g., for the https://JoelMarcey.github.io site, it would be set like...
  //   organizationName: 'JoelMarcey'

  // For no header links in the top nav bar -> headerLinks: [],
  headerLinks: [
    {page: 'index', label: 'Home'},
    {href: 'index.html#quemusa', label: 'Quem Usa?'},
    {blog: true, label: 'Blog'},
    {href : "https://forum.ieducar.org", label: 'Fórum' },
    {page: "docs", label: 'Documentação'},
  ],

  // If you have users set above, you add it here:
  users,
  statistics,

  /* path to images for header/footer */
  headerIcon: 'img/logo_horizontal.svg',
  footerIcon: 'img/logo_ieducar_horizontal_footer.svg',
  favicon: 'img/i-educar-logo.ico',

  /* colors for website */
  colors: {
    primaryColor: '#2696ff',
    // secondaryColor: '#3ee4cf',
    secondaryColor: '#2696ff',
  },

  /* custom fonts for website */
  /*fonts: {
    myFont: [
      "Times New Roman",
      "Serif"
    ],
    myOtherFont: [
      "-apple-system",
      "system-ui"
    ]
  },*/
  fonts: {
    descriptionFont: [
      "Hind", "sans-serif"
    ],
    titleFont: [
      "Muli", "sans-serif"
    ]
  },
  blogSidebarCount: 'ALL',
  blogSidebarTitle: { default: 'Últimas Notícias', all: 'Todas as notícias' },
  // This copyright info is used in /core/Footer.js and blog rss/atom feeds.
  copyright:
    'Copyright © ' +
    new Date().getFullYear() +
    ' Your Name or Your Company Name',

  highlight: {
    // Highlight.js theme to use for syntax highlighting in code blocks
    theme: 'default',
  },

  // Add custom scripts here that would be placed in <script> tags
  scripts: ['https://buttons.github.io/buttons.js'],

  /* On page navigation for the current documentation page */
  onPageNav: 'separate',

  /* Open Graph and Twitter card images */
  ogImage: 'img/docusaurus.png',
  twitterImage: 'img/docusaurus.png',
  gaTrackingId: 'UA-122039826-1',
  gaGtag: true,
  scripts: [
    'js/mailchimp.js',
    'js/map.js',
    'js/conversao.js',
  ],
  mapsApiKey: 'AIzaSyCIThOGkNPz5Kxk1CH5on42LzEMpVLGhho',

  // You may provide arbitrary config keys to be used as needed by your
  // template. For example, if you need your repo's URL...
  //   repoUrl: 'https://github.com/facebook/test-site',
};

module.exports = siteConfig;
