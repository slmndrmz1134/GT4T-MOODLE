"""Build the privacy notice PDF linked on the login page from privacy-notice.md.

The Markdown is kept simple: "# " starts a document (each on a new page), "## " a section, "- " a list item,
"  - " a nested list item, **text** is bold; other lines are paragraphs.

Usage (Python 3 with reportlab):  python docs/privacy/build_pdf.py
Output: theme/diverse/policy/diverse-privacy-notice.pdf
"""
import pathlib
import re

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.units import mm
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import ListFlowable, ListItem, PageBreak, Paragraph, SimpleDocTemplate, Spacer

HERE = pathlib.Path(__file__).resolve().parent
SOURCE = HERE / 'privacy-notice.md'
OUT = HERE.parent.parent / 'theme' / 'diverse' / 'policy' / 'diverse-privacy-notice.pdf'

# A font with Turkish and other European characters: Segoe UI (Windows) or DejaVu Sans (Linux).
FONTS = [
    (r'C:\Windows\Fonts\segoeui.ttf', r'C:\Windows\Fonts\segoeuib.ttf'),
    ('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'),
]
regular, bold = next((r, b) for r, b in FONTS if pathlib.Path(r).exists())
pdfmetrics.registerFont(TTFont('Sans', regular))
pdfmetrics.registerFont(TTFont('Sans-Bold', bold))
pdfmetrics.registerFontFamily('Sans', normal='Sans', bold='Sans-Bold')

INK = colors.HexColor('#1F2328')
ORANGE = colors.HexColor('#C2410C')
MUTED = colors.HexColor('#5B616B')

title = ParagraphStyle('title', fontName='Sans-Bold', fontSize=18, leading=23, textColor=INK, spaceAfter=4)
section = ParagraphStyle('section', fontName='Sans-Bold', fontSize=12, leading=16, textColor=ORANGE,
                         spaceBefore=10, spaceAfter=4)
body = ParagraphStyle('body', fontName='Sans', fontSize=9.5, leading=13.5, textColor=INK, spaceAfter=5)
meta = ParagraphStyle('meta', parent=body, textColor=MUTED, spaceAfter=8)


def inline(text):
    text = text.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;')
    return re.sub(r'\*\*(.+?)\*\*', r'<b>\1</b>', text)


def bullets(items):
    flow = []
    for text, children in items:
        content = [Paragraph(inline(text), body)]
        if children:
            content.append(bullets([(c, []) for c in children]))
        flow.append(ListItem(content, leftIndent=12))
    return ListFlowable(flow, bulletType='bullet', start='•', leftIndent=12, bulletFontName='Sans')


def build():
    story = []
    items = []
    first_document = True

    def flush():
        if items:
            story.append(bullets(list(items)))
            items.clear()

    lines = SOURCE.read_text(encoding='utf-8').splitlines()
    for i, line in enumerate(lines):
        if line.startswith('# '):
            flush()
            if not first_document:
                story.append(PageBreak())
            first_document = False
            story.append(Paragraph(inline(line[2:]), title))
        elif line.startswith('## '):
            flush()
            story.append(Paragraph(inline(line[3:]), section))
        elif line.startswith('  - '):
            items[-1][1].append(line[4:])
        elif line.startswith('- '):
            items.append((line[2:], []))
        elif line.strip():
            flush()
            # The version line right under a title.
            style = meta if i > 0 and lines[i - 2].startswith('# ') else body
            story.append(Paragraph(inline(line), style))
    flush()

    def footer(canvas, doc):
        canvas.saveState()
        canvas.setFont('Sans', 8)
        canvas.setFillColor(MUTED)
        canvas.drawString(18 * mm, 10 * mm, 'DIVERSE Learning Platform · beykent-gt4t@beykent.edu.tr')
        canvas.drawRightString(A4[0] - 18 * mm, 10 * mm, str(doc.page))
        canvas.restoreState()

    OUT.parent.mkdir(parents=True, exist_ok=True)
    doc = SimpleDocTemplate(str(OUT), pagesize=A4, leftMargin=18 * mm, rightMargin=18 * mm, topMargin=16 * mm,
                            bottomMargin=18 * mm, title='DIVERSE Learning Platform – Privacy Notice and Terms of Use',
                            author='Istanbul Beykent University (GT4T)')
    doc.build(story + [Spacer(1, 1)], onFirstPage=footer, onLaterPages=footer)
    print(OUT)


build()
