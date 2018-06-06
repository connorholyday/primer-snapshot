const puppeteer = require('puppeteer');
const { toMatchImageSnapshot } = require('jest-image-snapshot');
const data = require('../patternsToTest.json');

expect.extend({ toMatchImageSnapshot });

describe('Snapshots match', function() {
    const server = `http://localhost:${data['port']}/patterns`;
    const patterns = data['patterns'];
    let browser;
    let page;

    if (!patterns) {
        test('Patterns have been passed in', done => {
            done.fail(new Error('No patterns accessible'));
        });
        return;
    }

    beforeEach(async function() {
        browser = await puppeteer.launch();
        page = await browser.newPage();

        page.setViewport({
            width: 800,
            height: 600,
        });
    });

    afterEach(() => browser.close());

    data['patterns'].forEach(({ name, url }) => {

        it(`Component: ${name}`, async () => {
            await page.goto(`${server}/${url}`);
            const image = await page.screenshot();

            expect(image).toMatchImageSnapshot({
                customSnapshotIdentifier: name,
            });
        });
    });
});
