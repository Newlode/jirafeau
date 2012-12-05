#!/bin/sh
# 
#   Jyraphe, your web file repository
#   Copyright (C) 2008  Julien "axolotl" BERNARD <axolotl@magieeternelle.org>
# 
#   This program is free software: you can redistribute it and/or modify
#   it under the terms of the GNU Affero General Public License as
#   published by the Free Software Foundation, either version 3 of the
#   License, or (at your option) any later version.
# 
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU Affero General Public License for more details.
# 
#   You should have received a copy of the GNU Affero General Public License
#   along with this program.  If not, see <http://www.gnu.org/licenses/>.
# 

WD="`dirname $0`"

PACKAGE=Jyraphe
POTFILES=`ls ${WD}/pub/*.php ${WD}/pub/lib/*.php ${WD}/pub/lib/template/*.php`
POT="${WD}/l10n/${PACKAGE}.pot"

xgettext --language=PHP --output "${WD}/${PACKAGE}.po" ${POTFILES}

if cmp -s "${WD}/${PACKAGE}.po" "${POT}"
then
  rm -f "${WD}/${PACKAGE}.po"
else
  mv -f "${WD}/${PACKAGE}.po" "${WD}/l10n/${PACKAGE}.pot"
fi

for PO in `ls ${WD}/l10n/po/*.po`
do
  LANG=`basename "${PO}" | sed 's/.po//'`
  if msgmerge "${PO}" "${POT}" > "${WD}/${LANG}.pot"
  then
    mv -f "${WD}/${LANG}.pot" "${PO}"
    echo "msgmerge of ${LANG} succeeded"
  else
    echo "msgmerge of ${LANG} failed"
    rm -f "${WD}/${LANG}.pot"
  fi
done
