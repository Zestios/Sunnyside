// package vars
const pkg = require('../../package.json')
const siteUrl = Cypress.env('host') ?? pkg.urls.live
const pagesUrls = []
const pages = pkg.globs.critical
pages.forEach((page) => {
  pagesUrls.push(siteUrl + page.url)
})

describe('Test critical urls', () => {
  if (pagesUrls.length) {
    pagesUrls.forEach((pageUrl) => {
      it(`Visits ${pageUrl}`, () => {
        cy.visit(pageUrl)
        cy.wait(1000)
        cy.request(pageUrl)
        .then((response) => {
          expect(response).property('status').to.equal(200)
        })
      })
    })
  }
})
