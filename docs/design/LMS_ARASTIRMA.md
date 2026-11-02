# LMS Karşılaştırması ve Uygulanan Kararlar

Giriş sonrası ekranlar yeniden tasarlanırken (Kasım 2026) diğer LMS'lere bakıldı. Bu belge neyin nereden alındığını ve
neyin yapılmadığını anlatır. Kaynakların çoğu üniversite yardım sayfalarıdır; üretici belgesi ya da hakemli çalışma
değildir, bu yüzden bulgular yön gösterici kabul edilmelidir.

## Moodle'da görülen sorunlar (öğrenci hesabıyla ölçüldü)

- Dashboard boşken ekranı "Timeline" ve dev bir takvim dolduruyordu (sayfa 1950 px); dersler dashboard'da yoktu.
- "Derslerim" dar ekranda "More" menüsünün altına düşüyordu.
- Öğrenciye "Edit mode" düğmesi gösteriliyordu.
- Ders sayfasında ilerleme, öğretmen ya da "sıradaki adım" yoktu; içeriği olmayan "New section" bölümleri görünüyordu.
- Öğretmen ve yönetici için ayrı bir ana ekran yoktu.

## Kim ne yapıyor, biz ne aldık

| Ürün | Örüntü | DIVERSE'teki karşılığı |
|---|---|---|
| Canvas, Blackboard Ultra | Sayfalardan bağımsız, kalıcı sol gezinme | Sol menü; telefonda alt sekme çubuğu |
| Blackboard Ultra | Etkinlik akışı: Important / Upcoming / Today / Recent | Yapılacaklar: Geciken / Bugün / Bu hafta / Daha sonra |
| Google Classroom | Assigned / Missing / Done ayrımı | Geciken işler ayrı ve en üstte |
| Brightspace | "Work To Do": elle liste yapmadan geciken ve yaklaşan işler | Liste Moodle takviminin işlerinden kendiliğinden gelir |
| Canvas (yeni dashboard) | Son notlar ve geri bildirim, ilerleme özeti | Son notlar paneli, ders kartlarında ilerleme |
| Coursera | Derse tek düğmeyle dönme (Resume) | "Kaldığınız yerden devam edin" kartı, ders sayfasında "Devam et" |
| Moodle 4+ | Ders dizini ve tamamlama işaretleri | Korundu, DIVERSE renklerine bağlandı |

Westfield State öğrenci anketi ve 2025 tarihli bir Avustralya çalışması da aynı yöne işaret ediyor: öğrenciler ilerlemeyi
kolayca izlemek ve tutarlı bir gezinme istiyor, bilgi yığılmasından ve tutarsız düzenden şikâyet ediyor.

## Roller

Panom ekranı role göre bölüm bölüm kurulur; bir kişi hem öğretip hem öğreniyorsa ikisini de görür.

- **Öğrenci:** devam et kartı, yapılacaklar, son notlar, dersler.
- **Öğretmen:** ders, öğrenci ve notlanacak teslim sayıları; not bekleyenler listesi; ders kartlarında Katılımcılar ve Notlar.
  Düzenleme yetkisi olmayan öğretmen de "öğretmen" sayılır (ölçüt: `moodle/grade:viewall`).
- **Yönetici:** site rakamları, partner tablosu, onay bekleyen hesaplar, sık işler, zamanlanmış görev durumu. Yönetici
  derslere çoğunlukla destek için kayıtlı olduğundan öğretmen bölümü ona gösterilmez.

## Bilerek yapılmayanlar

- **Duyurular paneli** (Canvas ve Blackboard'da var): forum eklentisinin iç verisine bağlanmak gerekiyordu, sonraya kaldı.
- **Dashboard'u kişiselleştirme:** Moodle'ın blok düzenleme modu dashboard'da kapatıldı. Yöneticinin eklediği blokların
  gösterimi sürer; varsayılan Timeline ve takvim blokları gizlenir, silinmez.
- **Tamamlama takibi:** ilerleme çubuğu ancak derste etkinlik tamamlama açıksa görünür. Bu bir ders ayarıdır, tema
  zorlamaz.

## Kaynaklar

- [Canvas: How do I use the Dashboard as a student?](https://community.canvaslms.com/docs/DOC-4431)
- [Canvas yeni öğrenci dashboard'u (Conroe ISD)](https://kb.conroeisd.net/kb/personalizing-the-canvas-dashboard-with-student-widgets)
- [Blackboard Activity Stream (GVSU)](https://services.gvsu.edu/TDClient/60/Portal/KB/PrintArticle?ID=12012)
- [Blackboard Ultra Base Navigation (Trinity College Dublin)](https://www.tcd.ie/itservices/vle/student-guides/getting-started/blackboard-ultra-base-navigation/)
- [Brightspace Work To Do (Minnesota State)](https://servicedesk.minnstate.edu/TDClient/30/Portal/KB/PrintArticle?ID=689)
- [Google Classroom: see your work](https://support.google.com/edu/classroom/answer/6020284)
- [University of Warwick Coursera learner guide](https://warwick.ac.uk/services/its/servicessupport/academictechnology/ldcu/workshops/university_of_warwick_-_coursera_learner_guide.pdf)
- [Moodle: New for students](https://docs.moodle.org/4x/sv/New_for_students)
- [Westfield State öğrenci anketi](https://www.westfield.ma.edu/plato/wp-content/uploads/FINAL-Student-Survey-Report.pdf)
- [Avustralya'da STEM öğrencileri ile LMS çalışması (2025)](https://aisel.aisnet.org/siged2025/12)
