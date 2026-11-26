<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Enums\MenuLocation;
use App\Models\Article;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Tag;
use App\Support\Slug;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Садржај преузет са сајта www.gimza.edu.rs: категорије, новости, пројекти,
 * текстови страница, странице и мени.
 *
 * Наслови и датуми објаве су са старог сајта, а текстови су кратки сажеци.
 * Имена ученика су изостављена.
 */
class ContentSeeder extends Seeder
{
    /**
     * Категорије са старог сајта (WordPress), у два нивоа.
     *
     * @var array<string, array<string>>
     */
    public const CATEGORIES = [
        'Новости' => [
            'Такмичења',
            'Шампиони Гимназије',
            'Научни клуб',
            'Новинарска секција',
            'Рачунарство и информатика',
            'Немачки језик',
            'Румунски језик',
            'From Game to STEM',
            'Makers Lab',
            'School of Tomorrow',
            'Остало',
        ],
        'Документа' => [],
        'Настава' => [],
        'Учење' => ['Географија', 'Филозофија IV разред'],
        'Пројекти' => ['ИПА програм БГ-РС, јавне набавке, Еразмус'],
    ];

    public const TAGS = [
        'Математика', 'Историја', 'Географија', 'Роботика', 'Страни језици', 'Спорт',
        'Наука', 'Екологија', 'Предузетништво', 'Еразмус+', 'Хуманитарна акција',
        'Библиотека', 'Толеранција', 'Документа',
    ];

    /** @var array<string, Category> */
    protected array $categories = [];

    /** @var array<string, Tag> */
    protected array $tags = [];

    /** @var array<string, Article> */
    protected array $articles = [];

    public function run(): void
    {
        $this->seedCategories();
        $this->seedTags();
        $this->seedNews();
        $this->seedProjects();
        $this->seedPageTexts();
        $pages = $this->seedPages();
        $this->seedMenu($pages);
    }

    protected function seedCategories(): void
    {
        foreach (self::CATEGORIES as $parentName => $children) {
            $parent = Category::create(['name' => $parentName]);
            $this->categories[$parentName] = $parent;

            foreach ($children as $childName) {
                $this->categories["{$parentName} › {$childName}"] = Category::create([
                    'name' => $childName,
                    'slug' => Slug::make("{$parentName} {$childName}"),
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }

    protected function seedTags(): void
    {
        foreach (self::TAGS as $name) {
            $this->tags[$name] = Tag::create(['name' => $name]);
        }
    }

    /**
     * Новости са старог сајта (категорија „Новости“), од најновије ка старијој.
     */
    protected function seedNews(): void
    {
        $news = [
            'poslovni-izazov' => [
                'title' => 'Пословни изазов',
                'date' => '2026-11-19 21:20',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Такмичења',
                'tags' => ['Предузетништво'],
                'featured' => true,
                'text' => '<p>На регионалном такмичењу „Пословни изазов“ за средњошколце источне Србије, одржаном 10. новембра 2026. у Зајечару, два тима Гимназије освојила су прво и друго место.</p><p>Оба тима су се пласирала на државно такмичење. Такмичење организује „Достигнућа младих“ уз подршку фондација, универзитета и компанија.</p>',
            ],
            'medjunarodni-dan-tolerancije' => [
                'title' => 'Међународни дан толеранције',
                'date' => '2026-11-19 20:59',
                'type' => ArticleType::Vest,
                'category' => 'Новости',
                'tags' => ['Толеранција'],
                'text' => '<p>Поводом Међународног дана толеранције, 16. новембра, ученици одељења IV-3 са професорком социологије учествовали су у заједничкој радионици са ученицима Школе „Јелена Мајсторовић“.</p><p>Циљ сусрета био је међусобно упознавање и неговање толеранције и поштовања различитости.</p>',
            ],
            'opstinsko-takmicenje-u-rukometu' => [
                'title' => 'Општинско такмичење у рукомету',
                'date' => '2026-11-19 20:43',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Такмичења',
                'tags' => ['Спорт'],
                'text' => '<p>Рукометна екипа Гимназије освојила је прво место на општинском такмичењу у категорији омладинаца. Честитамо екипи и желимо јој успех на наредним нивоима такмичења.</p>',
            ],
            'vukobratovicevi-dani-robotike' => [
                'title' => 'Интернационално такмичење „Вукобратовићеви дани роботике“',
                'date' => '2026-11-19 20:17',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Шампиони Гимназије',
                'tags' => ['Роботика'],
                'featured' => true,
                'text' => '<p>Роботичарски тим Гимназије „ByteClub“ освојио је прво место у категорији Seniors 2 на међународном такмичењу „Вукобратовићеви дани роботике“, одржаном 6. и 7. новембра у Сава центру у Београду.</p>',
            ],
            'godisnji-plan-rada-skole' => [
                'title' => 'Годишњи план рада школе',
                'date' => '2026-11-04 14:17',
                'type' => ArticleType::Vest,
                'category' => 'Документа',
                'tags' => ['Документа'],
                'text' => '<p>Објављен је Годишњи план рада школе за школску 2026/27. годину.</p>',
            ],
            'dobrodoslica-u-svet-knjiga' => [
                'title' => 'Добродошлица у свет књига',
                'date' => '2026-11-04 14:15',
                'type' => ArticleType::Vest,
                'category' => 'Новости',
                'tags' => ['Библиотека'],
                'text' => '<p>Ученици првог разреда посетили су у октобру Матичну библиотеку „Светозар Марковић“ у Зајечару.</p><p>Библиотекари су им представили одељења и услуге библиотеке, електронски каталог и дигиталне базе података, са циљем да код ученика подстакну навику читања.</p>',
            ],
            'priznanja-povodom-dana-grada' => [
                'title' => 'Признања ученицима и професорки Гимназије Зајечар поводом Дана града',
                'date' => '2026-10-15 18:06',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости',
                'featured' => true,
                'text' => '<p>На свечаности поводом Дана града Зајечара ученици Гимназије добили су Повељу града за постигнуте резултате.</p><p>Професорка историје добитница је Октобарске награде Града Зајечара за допринос образовању.</p>',
            ],
            'prijem-roditelja' => [
                'title' => 'Пријем родитеља',
                'date' => '2026-10-06 18:01',
                'type' => ArticleType::Vest,
                'category' => 'Документа',
                'tags' => ['Документа'],
                'text' => '<p>Објављен је распоред термина за пријем родитеља у школској 2026/27. години.</p>',
            ],
            'bustic-edu' => [
                'title' => 'Гимназија Зајечар међу изабраним школама за пилотирање програма „BUSTIC EDU – Образовање за одрживост“',
                'date' => '2026-09-17 11:13',
                'type' => ArticleType::Vest,
                'category' => 'Новости',
                'tags' => ['Екологија'],
                'text' => '<p>Гимназија Зајечар је једна од три гимназије у Србији изабране за пилотирање програма „BUSTIC EDU – Образовање за одрживост“.</p><p>Од фебруара до јуна 2026. одржано је седам радионица о одрживом развоју и активном грађанству за ученике првог, другог и трећег разреда, а школа је добила средства за зелени зид.</p>',
            ],
            'uspeh-na-icyss-2026' => [
                'title' => 'Успех ученице Гимназије на интернационалном такмичењу ICYSS 2026',
                'date' => '2026-09-08 20:28',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Шампиони Гимназије',
                'tags' => ['Историја'],
                'text' => '<p>Ученица трећег разреда природно-математичког смера освојила је прво место за постер презентацију на Интернационалној конференцији младих друштвених научника (ICYSS 2026), одржаној од 3. до 7. септембра на Балију у Индонезији.</p>',
            ],
            'icyss-2026-najava' => [
                'title' => 'Интернационално такмичење – International Conference of Young Social Scientists 2026 (ICYSS)',
                'date' => '2026-09-06 13:37',
                'type' => ArticleType::Vest,
                'category' => 'Новости › Шампиони Гимназије',
                'tags' => ['Историја'],
                'text' => '<p>Ученица трећег разреда учествује на Интернационалној конференцији младих друштвених научника на Балију, од 3. до 7. септембра 2026, као чланица репрезентације Србије.</p><p>Учешће је изборила првим местом на државном такмичењу истраживачких радова из историје. Пут су помогли Град Зајечар и донатори.</p>',
            ],
            'priznanje-naucnom-klubu' => [
                'title' => 'Још једно признање Гимназији Зајечар за крај школске 2025/26. године!',
                'date' => '2026-08-31 18:40',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Научни клуб',
                'tags' => ['Наука'],
                'text' => '<p>Научни клуб Гимназије добио је захвалницу Центра за промоцију науке за промоцију и популаризацију науке.</p><p>Признање је уручено 31. августа 2026. у Ботаничкој башти „Јевремовац“ у Београду.</p>',
            ],
            'drzavno-takmicenje-talentovanih' => [
                'title' => 'Државно такмичење талентованих ученика средњих школа',
                'date' => '2026-06-17 15:23',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Шампиони Гимназије',
                'tags' => ['Историја', 'Страни језици'],
                'text' => '<p>На 68. државном такмичењу талентованих ученика средњих школа, одржаном 30. маја 2026. у Панчеву, ученици Гимназије освојили су треће место из енглеског језика, као и прво и треће место из историје.</p><p>Такмичење се састојало од истраживачких радова и теста знања.</p>',
            ],
            'kengur-bez-granica' => [
                'title' => '„Кенгур без граница“',
                'date' => '2026-06-09 16:15',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Шампиони Гимназије',
                'tags' => ['Математика'],
                'text' => '<p>Два ученика одељења IV-4 пласирала су се у финале математичког такмичења „Кенгур без граница“, одржано 7. јуна 2026, на основу награда освојених у првом кругу. У финалу је један од њих освојио трећу награду.</p>',
            ],
            'arhimedes' => [
                'title' => 'Успех гимназијалаца на Математичком турниру „Архимедес“',
                'date' => '2026-06-09 15:53',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Шампиони Гимназије',
                'tags' => ['Математика'],
                'text' => '<p>На 47. математичком турниру „Архимедес“, екипном првенству средњих школа, ученици Гимназије освојили су трећу награду и похвалу, а похваљена је и екипа школе.</p>',
            ],
            'drzavno-takmicenje-iz-istorije' => [
                'title' => 'Државно такмичење из историје ученика гимназија и средњих стручних школа',
                'date' => '2026-05-25 20:31',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Такмичења',
                'tags' => ['Историја'],
                'text' => '<p>Државно такмичење из историје одржано је 23. маја 2026. у Сремској Митровици. Од три ученика Гимназије који су учествовали, ученик првог разреда освојио је треће место у својој категорији.</p>',
            ],
            'novi-uspesi-na-republickim-takmicenjima' => [
                'title' => 'Нови успеси наших ученица на републичким такмичењима',
                'date' => '2026-05-15 17:20',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Шампиони Гимназије',
                'tags' => ['Страни језици', 'Историја'],
                'text' => '<p>Ученице Гимназије постигле су запажене резултате на републичким такмичењима: прво место из шпанског језика, четврто место са похвалом из италијанског језика, као и прво и друго место на смотри истраживачких радова из историје.</p>',
            ],
            'siroko-srce' => [
                'title' => 'Признање „Широко срце“ Црвеног крста Зајечар',
                'date' => '2026-05-11 10:14',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости',
                'tags' => ['Хуманитарна акција'],
                'text' => '<p>Поводом Националног дана добровољних давалаца крви, Црвени крст Зајечар доделио је Гимназији плакету „Широко срце“ као најхуманијој средњој школи по броју ученика који дају крв.</p><p>Похваљени су и ученици волонтери, а признања су добили и ученици који су први пут дали крв.</p>',
            ],
            'nacionalna-geografska-olimpijada' => [
                'title' => 'Национална географска олимпијада',
                'date' => '2026-04-27 21:05',
                'type' => ArticleType::Uspeh,
                'category' => 'Новости › Шампиони Гимназије',
                'tags' => ['Географија'],
                'text' => '<p>Ученик другог разреда освојио је другу награду на Националној географској олимпијади, одржаној од 24. до 26. априла 2026.</p>',
            ],
        ];

        foreach ($news as $key => $item) {
            $this->article($key, $item);
        }
    }

    /**
     * Пројекти Гимназије (на старом сајту су у поткатегоријама новости).
     */
    protected function seedProjects(): void
    {
        $projects = [
            'from-game-to-stem' => [
                'title' => 'From Game to STEM (Од игре до СТЕМ-а)',
                'date' => '2022-02-12 10:00',
                'type' => ArticleType::Projekat,
                'category' => 'Новости › From Game to STEM',
                'tags' => ['Еразмус+', 'Наука'],
                'url' => 'http://fromgametostem.com',
                'text' => '<p>Еразмус+ пројекат који Гимназија Зајечар координира са школама из Шпаније, Турске, Пољске и Бугарске.</p><p>Ученици кроз игру уче природне науке и технологију, савладавају програмирање Ардуина, а главни резултат пројекта је видео-игра за наставу. Током 2022. године организоване су размене ученика и наставника у свим партнерским земљама.</p>',
            ],
            'makers-lab' => [
                'title' => 'Makers Lab',
                'date' => '2023-11-16 10:00',
                'type' => ArticleType::Projekat,
                'category' => 'Новости › Makers Lab',
                'tags' => ['Екологија', 'Предузетништво'],
                'text' => '<p>Гимназија Зајечар је прва гимназија која је добила Makers Lab, простор за практичан и креативан рад ученика.</p><p>У оквиру лабораторије ученичка компанија „ZaČili“ проглашена је за најбољу ученичку компанију у Србији, уз подршку Фондације Nordeus уређена је паметна башта са пластеником, а одржавају се и радионице о зеленој агенди и животној средини.</p>',
            ],
            'skola-buducnosti' => [
                'title' => 'Школа будућности (School of Tomorrow)',
                'date' => '2019-11-24 21:38',
                'type' => ArticleType::Projekat,
                'category' => 'Новости › School of Tomorrow',
                'tags' => ['Еразмус+'],
                'url' => 'https://schooloftomorrowsoft.blogspot.com',
                'text' => '<p>Еразмус+ пројекат за увођење иновативних метода у наставу. Наставници су похађали обуке у Прагу и Фиренци о дигиталној и пројектној настави и учионици усмереној на ученика, а сарађивали су и са партнерском школом из Манавгата у Турској.</p><p>Стечена знања наставници преносе колегама у школи.</p>',
            ],
            'selma-hacking-hate' => [
                'title' => 'SELMA – Hacking Hate',
                'date' => '2019-10-23 10:00',
                'type' => ArticleType::Projekat,
                'category' => 'Новости › Остало',
                'url' => 'https://hackinghate.eu',
                'text' => '<p>Материјали пројекта SELMA (Social and Emotional Learning for Mutual Awareness) за ученике: како да одговорно комуницирају на друштвеним мрежама и да препознају и спрече говор мржње.</p>',
            ],
        ];

        foreach ($projects as $key => $item) {
            $this->article($key, $item);
        }
    }

    /**
     * Текстови за странице сајта (О школи, Историја, Колектив...).
     */
    protected function seedPageTexts(): void
    {
        $texts = [
            'tekst-gimnazija-zajecar' => [
                'title' => 'Гимназија Зајечар',
                'text' => '<p>Гимназија Зајечар основана је 1836. године. Једна је од најстаријих гимназија у Србији и прва средња школа у источној Србији.</p>'
                    .'<p>Школа данас има шеснаест одељења, по четири у сваком разреду, и уписује ученике на смерове:</p>'
                    .'<ul><li>Природно-математички смер</li><li>Друштвено-језички смер</li><li>Општи тип</li><li>Одељење за ученике са посебним способностима за рачунарство и информатику</li></ul>'
                    .'<p>Гимназија је прва гимназија у Србији која је добила Makers Lab и носилац је ознаке STEM School Label.</p>',
            ],
            'tekst-istorija' => [
                'title' => 'Историја Гимназије',
                'text' => '<h2>Почеци</h2>'
                    .'<p>Главна школа у Зајечару, са два гимназијска разреда, основана је 22. августа 1836. године, исте године када и школе у Шапцу и Чачку. Први наставник био је Живојин Керечки, а настава се одржавала у згради основне школе.</p>'
                    .'<p>Школа је 1839. премештена у Неготин, а затим враћена у Зајечар. Директор Стојан Бошковић основао је прву школску библиотеку.</p>'
                    .'<h2>Виша гимназија и нова зграда</h2>'
                    .'<p>Школске 1878/79. двогодишња реална школа прерасла је у нижу гимназију. Народна скупштина је 10. јуна 1884. у Нишу одлучила да Зајечар добије вишу гимназију, прву у источној Србији.</p>'
                    .'<p>Темељи нове зграде постављени су 1891, а школа се у њу уселила 1893. године. Гимназија је до 1904. носила име Доситеја Обрадовића, затим је названа Гимназија у Зајечару, а после уједињења Државна реална гимназија у Зајечару.</p>'
                    .'<h2>Ратови и промене</h2>'
                    .'<p>Рад школе више пута су прекидали ратови. Током Балканских ратова зграда је служила као болница, а после Првог светског рата била је тешко оштећена и остала без библиотеке.</p>'
                    .'<p>Школа је 1957. добила име „Моша Пијаде“, а крајем педесетих година пресељена је, па четири године касније прешла у нову зграду у школском центру, где се и данас налази. Почетком 1990. враћен јој је статус гимназије и назив Гимназија Зајечар.</p>'
                    .'<h2>Познати ученици</h2>'
                    .'<p>Гимназију су похађали Никола Пашић, Адам Богосављевић, Јован Атанацковић, Стеван Стојановић Мокрањац, Ђорђе Генчић, Петар Живковић, Милан Гавриловић, Бранко Лазаревић и Зоран Радмиловић.</p>',
            ],
            'tekst-ucenicki-parlament' => [
                'title' => 'Ученички парламент',
                'text' => '<p>Ученички парламент основан је школске 2002/03. године. Чине га 32 члана, по два представника из сваког одељења.</p>'
                    .'<p>Парламент организује традиционалне догађаје као што су маскенбал, журка добродошлице за ученике првог разреда и акција за 14. фебруар, као и пројекте о родној равноправности, људским правима и деци у медијима.</p>'
                    .'<p>Као члан Уније средњошколаца Србије, парламент сваке године спроводи пројекат „Средњошколци средњошколцима“, у којем учествује око сто ученика зајечарских средњих школа. Сарађује и са Зајечарском иницијативом, Клубом младих 019 и Центром за промоцију науке.</p>',
            ],
            'tekst-kolektiv' => [
                'title' => 'Колектив',
                'text' => $this->staffListHtml(),
            ],
            'tekst-plan-nastave' => [
                'title' => 'План наставе и учења',
                'text' => '<p>Гимназија Зајечар образује ученике на следећим смеровима:</p>'
                    .'<ul><li>Природно-математички смер</li><li>Друштвено-језички смер</li><li>Општи тип</li><li>Одељење за ученике са посебним способностима за рачунарство и информатику</li></ul>'
                    .'<h2>Изборни програми</h2>'
                    .'<p>Ученици првог разреда бирају два од четири изборна програма:</p>'
                    .'<ul><li>Здравље и спорт</li><li>Уметност и дизајн</li><li>Примењене науке</li><li>Језик, медији и култура</li></ul>',
            ],
            'tekst-upis' => [
                'title' => 'Упис у први разред',
                'text' => '<p>Подаци за упис у школској 2024/25. години:</p>'
                    .'<ul><li>Природно-математички смер (ZAZA GA 4R04S) – 56 ученика</li><li>Друштвено-језички смер (ZAZA GA 4R01S) – 56 ученика</li><li>Општи тип (ZAZA GA 4R03S)</li><li>Ученици са посебним способностима за рачунарство и информатику (ZAZA GA 4R31S)</li></ul>'
                    .'<h2>Бодовање</h2>'
                    .'<p>Кандидати се рангирају на основу успеха из шестог, седмог и осмог разреда (до 60 бодова), завршног испита (до 40 бодова) и награда са такмичења.</p>'
                    .'<h2>Пријемни испит за ИТ одељење</h2>'
                    .'<p>Кандидати за одељење за рачунарство и информатику полажу тест из математике: 12 задатака по 20 бодова, 120 минута. За пролаз је потребно најмање 120 од 240 бодова. Ученици са републичких такмичења из математике, физике и информатике могу добити бодове без полагања.</p>'
                    .'<p>Пријава се подноси електронски на порталу <a href="https://www.mojasrednjaskola.gov.rs">Моја средња школа</a> или лично у школи.</p>',
            ],
            'tekst-raspored-casova' => [
                'title' => 'Распоред часова',
                'text' => '<p>Распоред часова објављује се за свако одељење, од I-1 до IV-4.</p><p>Распоред је доступан и на порталу <a href="https://www.rasporednastave.gov.rs/">Распоред наставе</a>.</p>',
            ],
            'tekst-raspored-provera' => [
                'title' => 'Распоред писмених и контролних вежби',
                'text' => '<p>Професори заказују писмене и контролне вежбе у CMS-у. Распоред за текуће тромесечје објављује се као Excel табела у одељку Документа.</p><p>Једно одељење може имати највише једну проверу дневно и највише три провере недељно.</p>',
            ],
            'tekst-prijem-roditelja' => [
                'title' => 'Време пријема родитеља',
                'text' => '<p>Термини у којима одељењске старешине и предметни професори примају родитеље објављени су 6. октобра 2026. за школску 2026/27. годину.</p>',
            ],
            'tekst-satnica-zvona' => [
                'title' => 'Сатница звона',
                'status' => ArticleStatus::Nacrt,
                'text' => '<ul><li>1. час: 8.00–8.45</li><li>2. час: 8.50–9.35</li><li>3. час: 9.55–10.40</li></ul><p>НАПОМЕНА ЗА УРЕДНИКЕ: остатак сатнице је на старом сајту само као слика и треба га преписати пре објављивања.</p>',
            ],
            'tekst-kontakt' => [
                'title' => 'Контакт',
                'text' => '<p><strong>Гимназија Зајечар</strong><br>Кнегиње Љубице 5<br>19000 Зајечар, Србија</p>'
                    .'<p>Телефон: 019 422 564<br>Имејл: <a href="mailto:info@gimza.edu.rs">info@gimza.edu.rs</a></p>'
                    .'<p>Facebook: <a href="https://www.facebook.com/gimzajecar">facebook.com/gimzajecar</a></p>',
            ],
            'tekst-dokumenta-za-prenos' => [
                'title' => 'Документа са старог сајта (за пренос)',
                'status' => ArticleStatus::Nacrt,
                'text' => '<p>НАПОМЕНА ЗА УРЕДНИКЕ: ова документа су на старом сајту и треба их отпремити у одељак Документа.</p>'
                    .'<ul>'
                    .'<li><a href="https://www.gimza.edu.rs/skolski-program/">Школски програм</a></li>'
                    .'<li><a href="https://www.gimza.edu.rs/statut-skole/">Статут школе</a></li>'
                    .'<li><a href="https://www.gimza.edu.rs/pravilnici-i-poslovnici-skole/">Правилници и пословници школе</a></li>'
                    .'<li><a href="https://www.gimza.edu.rs/razvojni-plan-skole/">Развојни план школе</a></li>'
                    .'<li><a href="https://www.gimza.edu.rs/godisnji-plan-rada-skole/">Годишњи план рада школе</a></li>'
                    .'<li>Информатор о раду и Финансије (страница „Документа“ на старом сајту)</li>'
                    .'<li><a href="http://www.gimza.edu.rs/wp-content/uploads/2024/12/Kalendar-izmenjeni-2024-2025.pdf">Календар рада</a></li>'
                    .'<li><a href="http://www.gimza.edu.rs/wp-content/uploads/2024/07/Одлука-о-избору-уџбеника-2023-измењена-2.pdf">Уџбеници 2023–2027.</a></li>'
                    .'<li><a href="http://www.gimza.edu.rs/wp-content/uploads/2021/01/uputstvo_za_maturski_rad2-v1.pdf">Упутство за израду матурских радова</a></li>'
                    .'<li><a href="http://www.gimza.edu.rs/wp-content/uploads/2020/10/Пројектна-настава-Приручник-са-идејама.pdf">Приручник за пројектну наставу</a></li>'
                    .'<li><a href="http://www.gimza.edu.rs/wp-content/uploads/2022/12/поглед-2.pdf">Часопис „Поглед“</a></li>'
                    .'<li><a href="http://www.gimza.edu.rs/wp-content/uploads/2024/10/BROJ-IZVRSILACA_2024.pdf">Број извршилаца</a></li>'
                    .'</ul>',
            ],
        ];

        // Текстови страница немају датум на старом сајту; воде се од почетка школске године.
        foreach ($texts as $key => $item) {
            $this->article($key, [
                'type' => ArticleType::Tekst,
                'date' => '2026-09-01 06:00',
                'slug' => Slug::make($item['title']),
                ...$item,
            ]);
        }
    }

    /**
     * @return array<string, Page>
     */
    protected function seedPages(): array
    {
        // slug => [наслов, надстраница, редослед, чланци, видљиво]
        $definitions = [
            'o-skoli' => ['О школи', null, 1, ['tekst-gimnazija-zajecar']],
            'istorija-gimnazije' => ['Историја Гимназије', 'o-skoli', 1, ['tekst-istorija']],
            'ucenicki-parlament' => ['Ученички парламент', 'o-skoli', 2, ['tekst-ucenicki-parlament']],
            'kolektiv' => ['Колектив', 'o-skoli', 3, ['tekst-kolektiv']],
            'plan-nastave-i-ucenja' => ['План наставе и учења', 'o-skoli', 4, ['tekst-plan-nastave']],
            'upis' => ['Упис', null, 2, ['tekst-upis']],
            'za-ucenike' => ['За ученике', null, 3, []],
            'raspored-casova' => ['Распоред часова', 'za-ucenike', 1, ['tekst-raspored-casova']],
            'raspored-pismenih-i-kontrolnih-vezbi' => ['Распоред писмених и контролних вежби', 'za-ucenike', 2, ['tekst-raspored-provera']],
            'vreme-prijema-roditelja' => ['Време пријема родитеља', 'za-ucenike', 3, ['tekst-prijem-roditelja', 'prijem-roditelja']],
            'satnica-zvona' => ['Сатница звона', 'za-ucenike', 4, ['tekst-satnica-zvona'], false],
            'projekti' => ['Пројекти', null, 4, ['from-game-to-stem', 'makers-lab', 'skola-buducnosti', 'selma-hacking-hate']],
            'kontakt' => ['Контакт', null, 5, ['tekst-kontakt']],
        ];

        $pages = [];

        foreach ($definitions as $slug => $definition) {
            [$title, $parent, $order, $articleKeys] = $definition;

            $page = Page::create([
                'title' => $title,
                'slug' => $slug,
                'parent_id' => $parent ? $pages[$parent]->id : null,
                'order' => $order,
                'is_active' => $definition[4] ?? true,
            ]);

            foreach ($articleKeys as $index => $key) {
                $page->pageArticles()->create(['article_id' => $this->articles[$key]->id, 'order' => $index]);
            }

            $pages[$slug] = $page;
        }

        return $pages;
    }

    /**
     * Мени по узору на стари сајт. Ставка: [назив, линк или null, страница или null, подставке, нови прозор].
     *
     * @param  array<string, Page>  $pages
     */
    protected function seedMenu(array $pages): void
    {
        $main = [
            ['Насловна', '/', null],
            ['О школи', null, 'o-skoli', [
                ['Историја Гимназије', null, 'istorija-gimnazije'],
                ['Ученички парламент', null, 'ucenicki-parlament'],
                ['Колектив', null, 'kolektiv'],
                ['План наставе и учења', null, 'plan-nastave-i-ucenja'],
            ]],
            ['Новости', '/vesti', null],
            ['За ученике', null, 'za-ucenike', [
                ['Распоред часова', null, 'raspored-casova'],
                ['Распоред писмених и контролних вежби', null, 'raspored-pismenih-i-kontrolnih-vezbi'],
                ['Време пријема родитеља', null, 'vreme-prijema-roditelja'],
                ['Календар рада', 'http://www.gimza.edu.rs/wp-content/uploads/2024/12/Kalendar-izmenjeni-2024-2025.pdf', null, [], true],
                ['Уџбеници 2023–2027.', 'http://www.gimza.edu.rs/wp-content/uploads/2024/07/Одлука-о-избору-уџбеника-2023-измењена-2.pdf', null, [], true],
                ['Добро јутро Гимзо', 'https://www.youtube.com/channel/UC25_DNmIo857qvmA2-0rwTQ', null, [], true],
            ]],
            ['Упис', null, 'upis'],
            ['Пројекти', null, 'projekti'],
            ['Документа', '/dokumenta', null],
            ['Галерија', '/galerija', null],
            ['Мој есДневник', 'https://moj.esdnevnik.rs/login', null, [], true],
            ['Контакт', null, 'kontakt'],
        ];

        foreach ($main as $order => $item) {
            $parent = $this->menuItem(MenuLocation::Glavni, $item, $pages, $order);

            foreach ($item[3] ?? [] as $childOrder => $child) {
                $this->menuItem(MenuLocation::Glavni, $child, $pages, $childOrder, $parent);
            }
        }

        $footer = [
            ['Facebook', 'https://www.facebook.com/gimzajecar', null, [], true],
            ['есДневник', 'https://esdnevnik.rs', null, [], true],
            ['Распоред наставе', 'https://www.rasporednastave.gov.rs/', null, [], true],
            ['Јубилеј', 'http://www.jubilej.gimza.edu.rs/', null, [], true],
            ['Од игре до СТЕМ-а', 'http://fromgametostem.com', null, [], true],
            ['Еколошки отисак', 'http://www.thinkchoosereduce.com', null, [], true],
            ['Контакт', null, 'kontakt'],
        ];

        foreach ($footer as $order => $item) {
            $this->menuItem(MenuLocation::Podnozje, $item, $pages, $order);
        }
    }

    /**
     * Датуми новости су из RSS-а старог сајта (UTC време).
     *
     * @param  array{title: string, type: ArticleType, text: string, slug?: string, date?: string, category?: string, tags?: array<string>, featured?: bool, url?: string, status?: ArticleStatus}  $item
     */
    protected function article(string $key, array $item): Article
    {
        $status = $item['status'] ?? ArticleStatus::Objavljeno;
        $date = isset($item['date'])
            ? Carbon::parse($item['date'], 'UTC')->setTimezone(config('app.timezone'))
            : now();

        $article = new Article([
            'title' => $item['title'],
            'slug' => $item['slug'] ?? $key,
            'description' => $item['text'],
            'type' => $item['type'],
            'status' => $status,
            'category_id' => isset($item['category']) ? $this->categories[$item['category']]->id : null,
            'is_featured' => $item['featured'] ?? false,
            'external_url' => $item['url'] ?? null,
            'published_at' => $status === ArticleStatus::Objavljeno ? $date : null,
        ]);

        // Датум са старог сајта, да би редослед новости био исти.
        $article->created_at = $date;
        $article->updated_at = $date;
        $article->save();

        $article->tags()->attach(collect($item['tags'] ?? [])->map(fn (string $name) => $this->tags[$name]->id));

        return $this->articles[$key] = $article;
    }

    /**
     * @param  array{0: string, 1: ?string, 2: ?string, 3?: array<mixed>, 4?: bool}  $item
     * @param  array<string, Page>  $pages
     */
    protected function menuItem(MenuLocation $menu, array $item, array $pages, int $order, ?MenuItem $parent = null): MenuItem
    {
        return MenuItem::create([
            'menu' => $menu,
            'label' => $item[0],
            'url' => $item[1],
            'page_id' => $item[2] ? $pages[$item[2]]->id : null,
            'parent_id' => $parent?->id,
            'order' => $order,
            'is_external' => $item[4] ?? false,
            'is_active' => true,
        ]);
    }

    /**
     * Списак колектива по групама, као на страници „Колектив“ старог сајта.
     */
    protected function staffListHtml(): string
    {
        $html = '';

        foreach (SchoolSeeder::STAFF as $group => $data) {
            $html .= '<h3>'.e($group).'</h3><ul>';

            foreach ($data['people'] as $name => $items) {
                $note = ($items !== [] && $items !== [$group]) ? ' – '.implode(', ', $items) : '';
                $html .= '<li>'.e($name.$note).'</li>';
            }

            $html .= '</ul>';
        }

        $html .= '<h3>Административно и техничко особље</h3><ul>';

        foreach (SchoolSeeder::SUPPORT_STAFF as $name => $role) {
            $html .= '<li>'.e("{$name} – {$role}").'</li>';
        }

        return $html.'</ul>';
    }
}
